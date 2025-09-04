<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    protected $paymentService;

    public function __construct(OrderService $orderService, PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $orders = auth('sanctum')->user()
            ->orders()
            ->with(['items.product', 'addresses'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        if ($order->customer_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->load(['items.product', 'addresses', 'payments']);
        
        return new OrderResource($order);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|array',
            'shipping_address.first_name' => 'required|string|max:100',
            'shipping_address.last_name' => 'required|string|max:100',
            'shipping_address.address_line_1' => 'required|string',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
            'billing_address' => 'nullable|array',
            'coupon_code' => 'nullable|string',
            'payment_method' => 'required|in:stripe'
        ]);

        try {
            $customer = auth('sanctum')->user();
            
            $order = $this->orderService->createOrderFromCart(
                $customer,
                $request->shipping_address,
                $request->billing_address,
                $request->coupon_code
            );

            // Create payment intent
            $paymentResult = $this->paymentService->createPaymentIntent($order);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order' => new OrderResource($order),
                    'payment' => $paymentResult
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function confirmPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_intent_id' => 'required|string'
        ]);

        if ($order->customer_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        try {
            $success = $this->paymentService->confirmPayment(
                $request->payment_intent_id,
                $order
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed successfully',
                    'data' => new OrderResource($order->fresh())
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment confirmation failed'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function cancel(Order $order)
    {
        if ($order->customer_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled at this time'
            ], 400);
        }

        try {
            $this->orderService->updateOrderStatus($order, 'cancelled');

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => new OrderResource($order->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function track(Order $order)
    {
        if ($order->customer_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $trackingInfo = [
            'order_number' => $order->order_number,
            'status' => $order->status,
            'tracking_number' => $order->tracking_number,
            'shipped_at' => $order->shipped_at,
            'delivered_at' => $order->delivered_at,
            'timeline' => [
                [
                    'status' => 'pending',
                    'description' => 'Order placed',
                    'date' => $order->created_at,
                    'completed' => true
                ],
                [
                    'status' => 'processing',
                    'description' => 'Order being processed',
                    'date' => $order->status === 'processing' ? $order->updated_at : null,
                    'completed' => in_array($order->status, ['processing', 'shipped', 'delivered'])
                ],
                [
                    'status' => 'shipped',
                    'description' => 'Order shipped',
                    'date' => $order->shipped_at,
                    'completed' => in_array($order->status, ['shipped', 'delivered'])
                ],
                [
                    'status' => 'delivered',
                    'description' => 'Order delivered',
                    'date' => $order->delivered_at,
                    'completed' => $order->status === 'delivered'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $trackingInfo
        ]);
    }
}