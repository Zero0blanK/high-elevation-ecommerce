<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Payment;
use App\Models\ShoppingCart;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        
        if (!$customer) {
            return redirect()->route('customer.login')->with('error', 'Please login to proceed with checkout.');
        }

        $cartItems = ShoppingCart::with(['product.primaryImage'])
            ->where('customer_id', $customer->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return ($item->product->is_on_sale ? $item->product->sale_price : $item->product->price) * $item->quantity;
        });

        $taxRate = 0.08; // 8% tax rate - you can make this configurable
        $taxAmount = $subtotal * $taxRate;
        $shippingAmount = $subtotal >= 50 ? 0 : 9.99; // Free shipping over $50
        $total = $subtotal + $taxAmount + $shippingAmount;

        // Get customer addresses
        $shippingAddresses = $customer->addresses()->where('type', 'shipping')->get();
        $billingAddresses = $customer->addresses()->where('type', 'billing')->get();

        return view('checkout.index', compact(
            'cartItems',
            'subtotal',
            'taxAmount',
            'shippingAmount',
            'total',
            'customer',
            'shippingAddresses',
            'billingAddresses'
        ));
    }

    public function buyNow(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        
        if (!$customer) {
            return redirect()->route('customer.login')
                ->with('error', 'Please login to proceed with checkout.')
                ->with('redirect_after_login', 'checkout.buyNow')
                ->with('product_data', $request->only(['product_id', 'quantity']));
        }

        try {
            // Clear existing cart
            ShoppingCart::where('customer_id', $customer->id)->delete();

            // Add this product to cart
            ShoppingCart::create([
                'customer_id' => $customer->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);

            // Redirect to checkout
            return redirect()->route('checkout.index');

        } catch (\Exception $e) {
            \Log::error('Buy Now process failed: ' . $e->getMessage());
            return back()->with('error', 'Unable to process your request. Please try again.');
        }
    }

    public function process(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Please login to proceed.'], 401);
        }

        // Get cart items
        $cartItems = ShoppingCart::where('customer_id', $customer->id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        try {
            DB::beginTransaction();

            // Create the order
            $order = new Order([
                'customer_id' => $customer->id,
                'customer_address_id' => $request->shipping_address_id,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'currency' => 'USD',
                'subtotal' => $cartItems->sum(function ($item) {
                    return $item->quantity * ($item->product->is_on_sale ? $item->product->sale_price : $item->product->price);
                }),
                'shipping_amount' => 0, // Add your shipping calculation logic
                'tax_amount' => 0, // Add your tax calculation logic
                'notes' => $request->order_notes
            ]);

            $order->total_amount = $order->subtotal + $order->shipping_amount + $order->tax_amount;
            $order->save();

            // Create order items
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $product->is_on_sale ? $product->sale_price : $product->price,
                    'total_price' => $cartItem->quantity * ($product->is_on_sale ? $product->sale_price : $product->price)
                ]);
            }

            // Get shipping address
            $shippingAddress = CustomerAddress::where('id', $request->shipping_address_id)
                ->where('customer_id', $customer->id)
                ->firstOrFail();

            // Create shipping address
            OrderAddress::create([
                'order_id' => $order->id,
                'address_id' => $request->shipping_address_id
            ]);

            // Handle billing address
            $billingAddressId = $request->boolean('same_as_shipping') 
                ? $request->shipping_address_id 
                : $request->billing_address_id;

            OrderAddress::create([
                'order_id' => $order->id,
                'address_id' => $billingAddressId
            ]);

            // Clear the cart
            ShoppingCart::where('customer_id', $customer->id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'redirect_url' => route('checkout.success', ['orderNumber' => $order->order_number])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout process failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success($orderNumber)
    {
        $customer = Auth::guard('customer')->user();
        
        $order = Order::with(['items.product', 'shippingAddress', 'billingAddress'])
            ->where('order_number', $orderNumber)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }

    private function processPayment(Request $request, $amount)
    {
        // This is a simplified payment processing simulation
        // In a real application, you would integrate with actual payment gateways
        
        switch ($request->payment_method) {
            case 'credit_card':
                // Simulate credit card processing
                if (strlen($request->card_number) >= 13) {
                    return [
                        'success' => true,
                        'transaction_id' => 'cc_' . uniqid()
                    ];
                }
                return [
                    'success' => false,
                    'message' => 'Invalid credit card number.'
                ];
                
            case 'paypal':
                // Simulate PayPal processing
                return [
                    'success' => true,
                    'transaction_id' => 'pp_' . uniqid()
                ];
                
            case 'stripe':
                // Simulate Stripe processing
                return [
                    'success' => true,
                    'transaction_id' => 'stripe_' . uniqid()
                ];
                
            default:
                return [
                    'success' => false,
                    'message' => 'Invalid payment method.'
                ];
        }
    }
}