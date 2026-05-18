<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $filters = [
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'search' => $request->search
        ];

        $orders = $this->orderService->getOrdersWithFilters($filters);
        
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'addresses', 'payments']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        try {
            $data = $request->validated();
            $status = $data['status'];

            // "Quick Deliver" logic: If tracking number is provided and quick_deliver is checked,
            // we transition straight to delivered.
            if ($request->boolean('quick_deliver') && $request->filled('tracking_number')) {
                $status = 'delivered';
            }

            $updateData = [];
            if ($request->filled('tracking_number')) {
                $updateData['tracking_number'] = $data['tracking_number'];
            }
            if ($request->filled('shipping_method')) {
                $updateData['shipping_method'] = $data['shipping_method'];
            }
            if ($request->filled('notes')) {
                $updateData['notes'] = $data['notes'];
            }

            $this->orderService->updateOrderStatus($order, $status, $updateData);

            return back()->with('success', 'Order status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating order status: ' . $e->getMessage());
        }
    }

    public function printShippingLabel(Order $order)
    {
        $order->load(['customer', 'addresses']);

        $shippingAddr = $order->addresses->first(fn($a) => ($a->type ?? $a->address_type ?? '') === 'shipping')
            ?? $order->addresses->first();

        return view('admin.orders.shipping-label', compact('order', 'shippingAddr'));
    }

    public function refund(\Illuminate\Http\Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0|max:' . $order->total_amount,
            'reason' => 'required|string'
        ]);

        try {
            $payment = $order->payments()->where('status', 'completed')->first();
            
            if (!$payment) {
                return back()->with('error', 'No completed payment found for this order.');
            }

            app(\App\Services\PaymentService::class)->refundPayment(
                $payment, 
                $request->amount
            );

            return back()->with('success', 'Refund processed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error processing refund: ' . $e->getMessage());
        }
    }

    public function approveReturn(Order $order)
    {
        try {
            $this->orderService->approveReturn($order);
            return back()->with('success', 'Return request approved and order marked as refunded.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving return: ' . $e->getMessage());
        }
    }

    public function denyReturn(Order $order)
    {
        try {
            $this->orderService->denyReturn($order);
            return back()->with('success', 'Return request denied.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error denying return: ' . $e->getMessage());
        }
    }
}
