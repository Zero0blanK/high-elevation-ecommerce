<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\CartRepository;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService
{
    protected OrderRepository $orderRepository;
    protected CartRepository $cartRepository;

    public function __construct(OrderRepository $orderRepository, CartRepository $cartRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
    }

    public function createOrderFromCart(Customer $customer, array $shippingAddress, array $billingAddress = null, $couponCode = null): Order
    {
        return DB::transaction(function () use ($customer, $shippingAddress, $billingAddress, $couponCode) {
            $cartItems = $this->cartRepository->getCartWithProducts($customer->id, null);
            
            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            // Validate stock availability
            foreach ($cartItems as $cartItem) {
                if ($cartItem->product->stock_quantity < $cartItem->quantity) {
                    throw new \Exception("Insufficient stock for {$cartItem->product->name}");
                }
            }

            // Calculate totals
            $subtotal = $cartItems->sum(function ($item) {
                $price = $item->product->is_on_sale ? $item->product->sale_price : $item->product->price;
                return $price * $item->quantity;
            });
            
            $taxAmount = $this->calculateTax($subtotal);
            $shippingAmount = $this->calculateShipping($cartItems, $shippingAddress);
            
            $discountAmount = 0;
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    $coupon->increment('used_count');
                }
            }

            $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            // Create order
            $order = $this->orderRepository->create([
                'customer_id' => $customer->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending'
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                $price = $cartItem->product->is_on_sale 
                    ? $cartItem->product->sale_price 
                    : $cartItem->product->price;

                $this->orderRepository->createOrderItem($order->id, [
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $price,
                    'total_price' => $price * $cartItem->quantity,
                    'product_options' => $cartItem->product_options
                ]);

                // Update inventory
                app(InventoryService::class)->adjustStock(
                    $cartItem->product_id,
                    -$cartItem->quantity,
                    'sale',
                    $order
                );
            }

            // Clear cart
            $this->cartRepository->clearCart($customer->id, null);

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $status, array $data = []): Order
    {
        $this->orderRepository->update($order, array_merge(['status' => $status], $data));

        // Send notification based on status
        switch ($status) {
            case 'processing':
                Mail::to($order->customer->email)->send(new \App\Mail\OrderProcessing($order));
                break;
            case 'shipped':
                $this->orderRepository->update($order, ['shipped_at' => now()]);
                Mail::to($order->customer->email)->send(new \App\Mail\OrderShipped($order));
                break;
            case 'delivered':
                $this->orderRepository->update($order, ['delivered_at' => now()]);
                Mail::to($order->customer->email)->send(new \App\Mail\OrderDelivered($order));
                break;
            case 'cancelled':
                $this->restoreInventoryFromOrder($order);
                Mail::to($order->customer->email)->send(new \App\Mail\OrderCancelled($order));
                break;
        }

        return $order->fresh();
    }

    public function findOrderById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function findOrderByNumber(string $orderNumber): ?Order
    {
        return $this->orderRepository->findByOrderNumber($orderNumber);
    }

    public function findOrderByCustomer(int $orderId, int $customerId): ?Order
    {
        return $this->orderRepository->findByCustomer($orderId, $customerId);
    }

    public function findOrderByNumberAndCustomer(string $orderNumber, int $customerId): ?Order
    {
        return $this->orderRepository->findByOrderNumberAndCustomer($orderNumber, $customerId);
    }

    public function getOrderWithRelations(int $id, array $relations = []): ?Order
    {
        return $this->orderRepository->findWithRelations($id, $relations);
    }

    public function getOrdersByCustomer(int $customerId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->orderRepository->getOrdersByCustomerPaginated($customerId, $perPage, $filters);
    }

    public function getOrdersWithFilters(array $filters = []): LengthAwarePaginator
    {
        return $this->orderRepository->getOrdersWithFilters($filters);
    }

    public function getOrderCountsByStatus(int $customerId): array
    {
        return $this->orderRepository->getOrderCountsByStatus($customerId);
    }

    public function cancelOrder(Order $order): Order
    {
        if (!$order->canBeCancelled()) {
            throw new \Exception('This order cannot be cancelled.');
        }

        return $this->updateOrderStatus($order, 'cancelled');
    }

    public function confirmDelivery(Order $order): Order
    {
        if ($order->status !== 'shipped') {
            throw new \Exception('Order must be shipped before confirming delivery.');
        }

        return $this->updateOrderStatus($order, 'delivered');
    }

    public function createPayment(Order $order, array $paymentData): \App\Models\Payment
    {
        return $this->orderRepository->createPayment(array_merge($paymentData, [
            'order_id' => $order->id
        ]));
    }

    private function calculateTax(float $subtotal): float
    {
        // Implement tax calculation logic based on location
        return $subtotal * 0.08; // 8% tax rate example
    }

    private function calculateShipping($cartItems, array $shippingAddress): float
    {
        // Implement shipping calculation logic
        $totalWeight = $cartItems->sum(function ($item) {
            return ($item->product->weight ?? 0) * $item->quantity;
        });

        // Simple weight-based shipping
        if ($totalWeight <= 1) return 5.99;
        if ($totalWeight <= 5) return 9.99;
        return 14.99;
    }

    private function restoreInventoryFromOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            app(InventoryService::class)->adjustStock(
                $item->product_id,
                $item->quantity,
                'return',
                $order
            );
        }
    }
}