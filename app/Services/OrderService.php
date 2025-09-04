<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\ShoppingCart;
use App\Models\Customer;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function createOrderFromCart(Customer $customer, array $shippingAddress, array $billingAddress = null, $couponCode = null)
    {
        return DB::transaction(function () use ($customer, $shippingAddress, $billingAddress, $couponCode) {
            $cartItems = $customer->cartItems()->with('product')->get();
            
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
            $subtotal = $cartItems->sum('total_price');
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
            $order = Order::create([
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
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->product->discounted_price,
                    'total_price' => $cartItem->total_price,
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

            // Create addresses
            $order->addresses()->create(array_merge($shippingAddress, ['type' => 'shipping']));
            $order->addresses()->create(array_merge($billingAddress ?: $shippingAddress, ['type' => 'billing']));

            // Clear cart
            $customer->cartItems()->delete();

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $status, array $data = [])
    {
        $order->update(array_merge(['status' => $status], $data));

        // Send notification based on status
        switch ($status) {
            case 'processing':
                Mail::to($order->customer->email)->send(new \App\Mail\OrderProcessing($order));
                break;
            case 'shipped':
                $order->update(['shipped_at' => now()]);
                Mail::to($order->customer->email)->send(new \App\Mail\OrderShipped($order));
                break;
            case 'delivered':
                $order->update(['delivered_at' => now()]);
                Mail::to($order->customer->email)->send(new \App\Mail\OrderDelivered($order));
                break;
            case 'cancelled':
                $this->restoreInventoryFromOrder($order);
                Mail::to($order->customer->email)->send(new \App\Mail\OrderCancelled($order));
                break;
        }

        return $order;
    }

    public function getOrdersWithFilters(array $filters = [])
    {
        $query = Order::with(['customer', 'items.product']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    private function calculateTax($subtotal)
    {
        // Implement tax calculation logic based on location
        return $subtotal * 0.08; // 8% tax rate example
    }

    private function calculateShipping($cartItems, $shippingAddress)
    {
        // Implement shipping calculation logic
        $totalWeight = $cartItems->sum(function ($item) {
            return $item->product->weight * $item->quantity;
        });

        // Simple weight-based shipping
        if ($totalWeight <= 1) return 5.99;
        if ($totalWeight <= 5) return 9.99;
        return 14.99;
    }

    private function restoreInventoryFromOrder(Order $order)
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