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
    protected PaymentService $paymentService;

    public function __construct(
        CartRepository $cartRepository,
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        CartService $cartService,
        InventoryService $inventoryService,
        PaymentService $paymentService
    ) {
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->cartService = $cartService;
        $this->inventoryService = $inventoryService;
        $this->paymentService = $paymentService;
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
                'currency' => 'USD',
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

            switch ($paymentMethod) {
                case 'credit_card':
                    $result = $this->processStripePayment($order, $data);
                    break;
                    
                case 'paypal':
                    $result = $this->processPayPalPayment($order, $data);
                    break;
                    
                case 'gcash':
                    $result = $this->processGCashPayment($order, $customer);
                    break;
                    
                case 'cod':
                default:
                    // For COD, just mark as pending and redirect to success
                    $this->createPaymentRecord($order, 'cod', 'pending');
                    break;
            }

            // Clear the cart
            $this->cartService->clearCart($customer->id);

            return $result;
        });
    }

    /**
     * Process Stripe card payment
     */
    protected function processStripePayment(Order $order, array $data): array
    {
        $stripeSecret = config('services.stripe.secret');
        
        if (empty($stripeSecret)) {
            throw new \Exception('Stripe is not configured. Please contact support.');
        }

        try {
            \Stripe\Stripe::setApiKey($stripeSecret);

            // If a payment method ID was provided (from Stripe Elements)
            if (!empty($data['stripe_payment_method_id'])) {
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => (int) ($order->total_amount * 100),
                    'currency' => strtolower($order->currency),
                    'payment_method' => $data['stripe_payment_method_id'],
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'return_url' => route('checkout.success', ['orderNumber' => $order->order_number]),
                    'metadata' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                    ],
                ]);

                $this->createPaymentRecord($order, 'stripe', 
                    $paymentIntent->status === 'succeeded' ? 'completed' : 'pending',
                    $paymentIntent->id
                );

                if ($paymentIntent->status === 'succeeded') {
                    $order->update(['payment_status' => 'paid', 'status' => 'processing']);
                } elseif ($paymentIntent->status === 'requires_action') {
                    return [
                        'order' => $order,
                        'success' => true,
                        'requires_action' => true,
                        'client_secret' => $paymentIntent->client_secret,
                        'redirect_url' => route('checkout.success', ['orderNumber' => $order->order_number])
                    ];
                }
            }

            return [
                'order' => $order,
                'success' => true,
                'redirect_url' => route('checkout.success', ['orderNumber' => $order->order_number])
            ];

        } catch (\Stripe\Exception\CardException $e) {
            throw new \Exception('Card error: ' . $e->getMessage());
        }
    }

    /**
     * Process PayPal payment
     */
    protected function processPayPalPayment(Order $order, array $data): array
    {
        // PayPal payment is already captured on the frontend
        // We just need to verify and record it
        if (!empty($data['paypal_order_id'])) {
            $this->createPaymentRecord($order, 'paypal', 'completed', $data['paypal_order_id']);
            $order->update(['payment_status' => 'paid', 'status' => 'processing']);
        }

        return [
            'order' => $order,
            'success' => true,
            'redirect_url' => route('checkout.success', ['orderNumber' => $order->order_number])
        ];
    }

    /**
     * Process GCash payment via PayMongo
     */
    protected function processGCashPayment(Order $order, Customer $customer): array
    {
        $paymongoSecretKey = config('services.gcash.secret_key');
        
        if (empty($paymongoSecretKey)) {
            throw new \Exception('GCash (PayMongo) is not configured. Please contact support.');
        }

        try {
            // Create PayMongo source for GCash
            $response = Http::withBasicAuth($paymongoSecretKey, '')
                ->post('https://api.paymongo.com/v1/sources', [
                    'data' => [
                        'attributes' => [
                            'amount' => (int) ($order->total_amount * 100),
                            'redirect' => [
                                'success' => route('checkout.gcash.success', ['orderNumber' => $order->order_number]),
                                'failed' => route('checkout.gcash.failed', ['orderNumber' => $order->order_number]),
                            ],
                            'type' => 'gcash',
                            'currency' => 'PHP',
                            'billing' => [
                                'name' => $customer->first_name . ' ' . $customer->last_name,
                                'email' => $customer->email,
                                'phone' => $customer->phone ?? null,
                            ],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('PayMongo GCash source creation failed', ['response' => $response->json()]);
                throw new \Exception('Failed to create GCash payment source.');
            }

            $source = $response->json()['data'];
            
            $this->createPaymentRecord($order, 'gcash', 'pending', $source['id']);

            return [
                'order' => $order,
                'success' => true,
                'payment_url' => $source['attributes']['redirect']['checkout_url'],
                'redirect_url' => route('checkout.success', ['orderNumber' => $order->order_number])
            ];

        } catch (\Exception $e) {
            Log::error('GCash payment processing failed', ['error' => $e->getMessage()]);
            throw new \Exception('GCash payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a payment record
     */
    protected function createPaymentRecord(Order $order, string $gateway, string $status, ?string $transactionId = null): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'payment_method' => $gateway,
            'payment_gateway' => $gateway,
            'transaction_id' => $transactionId,
            'amount' => $order->total_amount,
            'currency' => $order->currency,
            'status' => $status,
            'gateway_response' => [],
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
     * Handle GCash success callback
     */
    public function handleGCashSuccess(string $orderNumber, int $customerId): Order
    {
        $order = $this->getOrderForSuccess($orderNumber, $customerId);
        
        $payment = Payment::where('order_id', $order->id)
            ->where('payment_gateway', 'gcash')
            ->first();

        if ($payment) {
            $payment->update(['status' => 'completed', 'processed_at' => now()]);
            $order->update(['payment_status' => 'paid', 'status' => 'processing']);
        }

        return $order;
    }

    /**
     * Handle GCash failure callback
     */
    public function handleGCashFailed(string $orderNumber, int $customerId): Order
    {
        $order = $this->getOrderForSuccess($orderNumber, $customerId);
        
        $payment = Payment::where('order_id', $order->id)
            ->where('payment_gateway', 'gcash')
            ->first();

        if ($payment) {
            $payment->update(['status' => 'failed']);
            $order->update(['payment_status' => 'failed']);
        }

        return $order;
    }
}
