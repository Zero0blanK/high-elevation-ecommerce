<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function index()
    {
        try {
            $customer = Auth::guard('customer')->user();
            $checkoutData = $this->checkoutService->getCheckoutData($customer);

            return view('checkout.index', $checkoutData);
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $customer = Auth::guard('customer')->user();
            
            $this->checkoutService->buyNow(
                $customer,
                $request->product_id,
                $request->quantity
            );

            return redirect()->route('checkout.index');
        } catch (\Exception $e) {
            Log::error('Buy Now process failed: ' . $e->getMessage());
            return back()->with('error', 'Unable to process your request. Please try again.');
        }
    }

    public function process(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:customer_addresses,id',
            'payment_method' => 'nullable|string',
            'same_as_shipping' => 'nullable|boolean',
            'billing_address_id' => 'nullable|exists:customer_addresses,id',
            'order_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $customer = Auth::guard('customer')->user();
            
            $result = $this->checkoutService->processCheckout($customer, $request->all());

            return response()->json([
                'success' => true,
                'order_number' => $result['order']->order_number,
                'redirect_url' => $result['redirect_url'],
                'payment_url' => $result['payment_url'] ?? null,
                'requires_action' => $result['requires_action'] ?? false,
                'client_secret' => $result['client_secret'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout process failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success($orderNumber)
    {
        try {
            $customer = Auth::guard('customer')->user();
            $order = $this->checkoutService->getOrderForSuccess($orderNumber, $customer->id);

            return view('checkout.success', compact('order'));
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }
    }

    public function gcashSuccess($orderNumber)
    {
        try {
            $customer = Auth::guard('customer')->user();
            $order = $this->checkoutService->handleGCashSuccess($orderNumber, $customer->id);

            return view('checkout.success', compact('order'));
        } catch (\Exception $e) {
            Log::error('GCash success callback failed: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Payment verification failed.');
        }
    }

    public function gcashFailed($orderNumber)
    {
        try {
            $customer = Auth::guard('customer')->user();
            $order = $this->checkoutService->handleGCashFailed($orderNumber, $customer->id);

            return redirect()->route('checkout.index')
                ->with('error', 'GCash payment failed. Please try again or use a different payment method.');
        } catch (\Exception $e) {
            Log::error('GCash failed callback failed: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'An error occurred. Please try again.');
        }
    }
}