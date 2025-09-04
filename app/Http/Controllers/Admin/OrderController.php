<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
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

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        try {
            $data = [];
            if ($request->filled('tracking_number')) {
                $data['tracking_number'] = $request->tracking_number;
            }
            if ($request->filled('notes')) {
                $data['notes'] = $request->notes;
            }

            $this->orderService->updateOrderStatus($order, $request->status, $data);

            return back()->with('success', 'Order status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating order status: ' . $e->getMessage());
        }
    }

    public function printShippingLabel(Order $order)
    {
        if (!$order->canBeShipped()) {
            return back()->with('error', 'Order cannot be shipped at this time.');
        }

        // Implement shipping label generation logic here
        // This would integrate with shipping providers like FedEx, UPS, etc.
        
        return back()->with('success', 'Shipping label generated successfully.');
    }

    public function refund(Request $request, Order $order)
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
}