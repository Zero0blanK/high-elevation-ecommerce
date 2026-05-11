<?php

namespace App\Services;

use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\CustomerRepository;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CheckoutService
{
    protected CartRepository $cartRepository;
    protected OrderRepository $orderRepository;
    protected CustomerRepository $customerRepository;
    protected CartService $cartService;
    protected InventoryService $inventoryService;

    public function __construct(
        CartRepository $cartRepository,
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        CartService $cartService,
        InventoryService $inventoryService
    ) {
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->cartService = $cartService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Get checkout data for the customer
     */
    public function getCheckoutData(Customer $customer): array
    {
        $cartTotals = $this->cartService->getCartTotals($customer->id);
        
        if ($cartTotals['items']->isEmpty()) {
            throw new \Exception('Your cart is empty.');
        }

        $subtotal = (float) $cartTotals['subtotal'];
        $taxRate = (float) config('ecommerce.tax.rate', 0.08);
        $freeShippingThreshold = (float) config('ecommerce.shipping.free_shipping_threshold', 50);
        $standardShippingRate = (float) config('ecommerce.shipping.rates.standard.base_rate', 5.99);

        $taxAmount = $subtotal * $taxRate;
        $shippingAmount = $subtotal >= $freeShippingThreshold ? 0.0 : $standardShippingRate;
        $total = $subtotal + $taxAmount + $shippingAmount;

        $shippingAddresses = $this->customerRepository->getAddressesByType($customer->id, 'shipping');
        $billingAddresses = $this->customerRepository->getAddressesByType($customer->id, 'billing');

        return [
            'cartItems' => $cartTotals['items'],
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'shippingAmount' => $shippingAmount,
            'total' => $total,
            'customer' => $customer,
            'shippingAddresses' => $shippingAddresses,
            'billingAddresses' => $billingAddresses,
        ];
    }

    /**
     * Process the checkout and create an order
     */
    public function processCheckout(Customer $customer, array $data): array
    {
        return DB::transaction(function () use ($customer, $data) {
            $cartTotals = $this->cartService->getCartTotals($customer->id);
            
            if ($cartTotals['items']->isEmpty()) {
                throw new \Exception('Your cart is empty.');
            }

            // Validate stock availability
            foreach ($cartTotals['items'] as $item) {
                if ($item->product->stock_quantity < $item->quantity) {
                    throw new \Exception("Insufficient stock for {$item->product->name}");
                }
            }

            // Calculate totals
            $subtotal = (float) $cartTotals['subtotal'];
            $taxRate = (float) config('ecommerce.tax.rate', 0.08);
            $freeShippingThreshold = (float) config('ecommerce.shipping.free_shipping_threshold', 50);
            $standardShippingRate = (float) config('ecommerce.shipping.rates.standard.base_rate', 5.99);

            $taxAmount = $subtotal * $taxRate;
            $shippingAmount = $subtotal >= $freeShippingThreshold ? 0.0 : $standardShippingRate;
            $totalAmount = $subtotal + $taxAmount + $shippingAmount;

            // Validate shipping address
            $shippingAddress = $this->customerRepository->findAddressByCustomer(
                $data['shipping_address_id'],
                $customer->id
            );

            if (!$shippingAddress) {
                throw new \Exception('Invalid shipping address.');
            }

            $paymentMethod = $data['payment_method'] ?? 'cod';
            $paymentStatus = $paymentMethod === 'cod' ? 'pending' : 'pending';

            // Create order
            $order = $this->orderRepository->create([
                'customer_id' => $customer->id,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'currency' => config('ecommerce.currency.default', 'PHP'),
                'subtotal' => $subtotal,
                'shipping_amount' => $shippingAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'notes' => $data['order_notes'] ?? null,
            ]);

            // Create order items
            foreach ($cartTotals['items'] as $item) {
                $price = $item->product->is_on_sale 
                    ? $item->product->sale_price 
                    : $item->product->price;

                $this->orderRepository->createOrderItem($order->id, [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $price,
                    'total_price' => $price * $item->quantity,
                    'product_options' => $item->product_options,
                ]);

                // Update inventory
                $this->inventoryService->adjustStock(
                    $item->product_id,
                    -$item->quantity,
                    'sale',
                    $order
                );
            }

            // Create order addresses
            $this->orderRepository->createOrderAddress($order->id, $data['shipping_address_id']);

            $billingAddressId = !empty($data['same_as_shipping']) 
                ? $data['shipping_address_id'] 
                : ($data['billing_address_id'] ?? $data['shipping_address_id']);

            $this->orderRepository->createOrderAddress($order->id, $billingAddressId);

            // Process payment based on method
            $result = [
                'order' => $order,
                'success' => true,
                'redirect_url' => route('checkout.success', ['orderNumber' => $order->order_number])
            ];

            if ($paymentMethod !== 'cod') {
                $gateway = \App\Services\Payment\PaymentGatewayFactory::create($paymentMethod);
                $paymentResult = $gateway->createPayment($order, $data);
                
                $this->createPaymentRecord(
                    $order,
                    $paymentMethod,
                    'pending',
                    $paymentResult['transaction_id'] ?? null,
                    $paymentResult['gateway_response'] ?? []
                );

                $result['payment_url'] = $paymentResult['payment_url'] ?? null;
                $result['requires_action'] = $paymentResult['requires_action'] ?? false;
                
                // Special handling for PayPal if already completed on frontend
                if ($paymentMethod === 'paypal' && !empty($data['paypal_order_id'])) {
                    $order->update(['payment_status' => 'paid', 'status' => 'processing']);
                    Payment::where('transaction_id', $data['paypal_order_id'])->update(['status' => 'completed']);
                }
            } else {
                $this->createPaymentRecord($order, 'cod', 'pending');
            }

            // Clear the cart
            $this->cartService->clearCart($customer->id);

            return $result;
        });
    }

    /**
     * Create a payment record
     */
    protected function createPaymentRecord(
        Order $order,
        string $gateway,
        string $status,
        ?string $transactionId = null,
        array $gatewayResponse = []
    ): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'payment_method' => $gateway,
            'payment_gateway' => $gateway,
            'transaction_id' => $transactionId,
            'amount' => $order->total_amount,
            'currency' => $order->currency,
            'status' => $status,
            'gateway_response' => $gatewayResponse,
        ]);
    }

    /**
     * Buy now - clear cart and add single product then redirect to checkout
     */
    public function buyNow(Customer $customer, int $productId, int $quantity = 1): void
    {
        // Clear existing cart
        $this->cartService->clearCart($customer->id);

        // Add this product to cart
        $this->cartService->addToCart($productId, $quantity, [], $customer->id);
    }

    /**
     * Get order for success page
     */
    public function getOrderForSuccess(string $orderNumber, int $customerId): Order
    {
        $order = $this->orderRepository->findByOrderNumberAndCustomer($orderNumber, $customerId);

        if (!$order) {
            throw new \Exception('Order not found.');
        }

        $order->load(['items.product', 'shippingAddress', 'billingAddress']);

        return $order;
    }

    /**
     * Handle PayMongo success callback
     */
    public function handlePayMongoSuccess(string $orderNumber, int $customerId): Order
    {
        $order = $this->getOrderForSuccess($orderNumber, $customerId);

        $payment = Payment::where('order_id', $order->id)
            ->whereIn('payment_gateway', ['paymongo', 'gcash', 'paymongo_card'])
            ->first();

        if ($payment) {
            $gatewayName = $payment->payment_gateway ?: $order->payment_method ?: 'paymongo';
            $gateway = \App\Services\Payment\PaymentGatewayFactory::create($gatewayName);
            $gateway->confirmPayment((string) $payment->transaction_id, $order);
        }

        return $order->fresh();
    }

    /**
     * Handle PayMongo failure callback
     */
    public function handlePayMongoFailed(string $orderNumber, int $customerId): Order
    {
        $order = $this->getOrderForSuccess($orderNumber, $customerId);

        $payment = Payment::where('order_id', $order->id)
            ->whereIn('payment_gateway', ['paymongo', 'gcash', 'paymongo_card'])
            ->first();

        return $this->failOrderPayment($order, $payment);
    }

    public function handleGCashSuccess(string $orderNumber, int $customerId): Order
    {
        return $this->handlePayMongoSuccess($orderNumber, $customerId);
    }

    public function handleGCashFailed(string $orderNumber, int $customerId): Order
    {
        return $this->handlePayMongoFailed($orderNumber, $customerId);
    }

    public function failOrderPayment(Order $order, ?Payment $payment = null): Order
    {
        return DB::transaction(function () use ($order, $payment) {
            $order->loadMissing(['items']);

            $payment ??= Payment::where('order_id', $order->id)
                ->whereIn('payment_gateway', ['paymongo', 'gcash', 'paymongo_card'])
                ->first();

            if ($payment && $payment->status !== 'failed') {
                $payment->update(['status' => 'failed']);
            }

            if ($order->payment_status !== 'failed') {
                $order->update(['payment_status' => 'failed']);
            }

            // Prevent duplicate cart/inventory restoration on repeated callbacks/webhooks.
            if ($order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    $this->inventoryService->adjustStock(
                        $item->product_id,
                        $item->quantity,
                        'return',
                        $order,
                        'Payment failed - stock restored'
                    );

                    $this->cartService->addToCart(
                        $item->product_id,
                        $item->quantity,
                        $item->product_options ?? [],
                        $order->customer_id
                    );
                }

                $order->update(['status' => 'cancelled']);
            }

            return $order->fresh();
        });
    }
}
