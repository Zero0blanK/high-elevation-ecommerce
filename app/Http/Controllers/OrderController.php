<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        
        // Get order counts for tabs
        $orderCounts = $this->orderService->getOrderCountsByStatus($customer->id);

        // Build query
        $query = $customer->orders()->with(['items.product']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'amount_high':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'amount_low':
                $query->orderBy('total_amount', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('account.orders', compact('orders', 'orderCounts'));
    }

    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated customer
        $customer = Auth::guard('customer')->user();
        
        if ($order->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Load all necessary relationships
        $order->load([
            'items.product.images',
            'customer',
            'customerAddress',
            'addresses',
        ]);

        // Get tracking data if the order has a tracking number
        $trackingData = null;
        if ($order->tracking_number) {
            $trackingData = $this->getTrackingData(
                $order->tracking_number,
                $order->shipping_method ?? 'standard'
            );
        }

        return view('account.order-details', compact('order', 'trackingData'));
    }

    public function showTrackingForm()
    {
        return view('orders.track');
    }

    public function trackByOrderNumber($orderNumber)
    {
        // Get the order by order number
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'customerAddress', 'customer'])
            ->first();

        if (!$order) {
            abort(404, 'Order not found.');
        }

        // Get tracking data if tracking number exists
        $trackingData = null;
        if ($order->tracking_number) {
            $trackingData = $this->getTrackingData($order->tracking_number, $order->shipping_method ?? 'standard');
        }

        return view('orders.show', compact('order', 'trackingData'));
    }

    public function trackByNumber(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email'
        ]);

        $order = Order::where('order_number', $request->order_number)
                    ->whereHas('customer', function($query) use ($request) {
                        $query->where('email', $request->email);
                    })
                    ->with(['items.product', 'customerAddress'])
                    ->first();

        if (!$order) {
            return back()->withErrors(['order_number' => 'Order not found or email does not match.']);
        }

        // Get tracking data if tracking number exists
        $trackingData = null;
        if ($order->tracking_number) {
            $trackingData = $this->getTrackingData($order->tracking_number, $order->shipping_method ?? 'standard');
        }

        return view('account.order-details', compact('order', 'trackingData'));
    }

    public function cancelOrder(Order $order)
    {
        $customer = Auth::guard('customer')->user();

        // Ensure the order belongs to the authenticated customer
        if ($order->customer_id !== $customer->id) {
            abort(403);
        }

        try {
            $this->orderService->cancelOrder($order);
            return back()->with('success', 'Order has been cancelled successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function confirmReceived(Order $order)
    {
        $customer = Auth::guard('customer')->user();

        // Ensure the order belongs to the authenticated customer
        if ($order->customer_id !== $customer->id) {
            abort(403);
        }

        try {
            $this->orderService->confirmDelivery($order);
            return back()->with('success', 'Order marked as received successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function returnOrder(Request $request, Order $order)
    {
        $customer = Auth::guard('customer')->user();

        if ($order->customer_id !== $customer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'return_reason' => 'required|string|max:1000',
        ]);

        if (!$order->canBeReturned()) {
            return back()->withErrors(['error' => 'This order is no longer eligible for return.']);
        }

        $this->orderService->requestReturn($order, $validated['return_reason']);

        return back()->with('success', 'Return request submitted. Waiting for admin approval.');
    }

    public function trackPackage(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
            'provider' => 'nullable|string'
        ]);

        $trackingData = $this->getTrackingData($request->tracking_number, $request->provider ?? 'standard');

        return response()->json($trackingData);
    }

    private function getTrackingData($trackingNumber, $provider)
    {
        $normalizedProvider = $this->normalizeTrackingProvider($provider);
        $providerLabel = $this->getTrackingProviderLabel($normalizedProvider);
        $trackingUrl = $this->buildTrackingUrl($normalizedProvider, $trackingNumber);
        $estimatedDelivery = now()->addDays(3)->format('Y-m-d');

        $trackingHistory = [];
        
        if ($normalizedProvider === 'jnt') {
            $trackingHistory = [
                [
                    'date' => now()->subDays(2)->format('Y-m-d H:i:s'),
                    'status' => 'Parcel is being prepared',
                    'location' => 'Seller Warehouse',
                    'description' => 'The seller is preparing to ship your parcel.',
                ],
                [
                    'date' => now()->subDays(1)->format('Y-m-d H:i:s'),
                    'status' => 'Picked up by J&T Express',
                    'location' => 'Drop-off Point',
                    'description' => 'Your parcel has been picked up and is now in transit.',
                ],
                [
                    'date' => now()->subHours(5)->format('Y-m-d H:i:s'),
                    'status' => 'Arrived at Sort Facility',
                    'location' => 'Regional Hub',
                    'description' => 'Your parcel is being sorted for delivery.',
                ],
                [
                    'date' => now()->format('Y-m-d H:i:s'),
                    'status' => 'Out for delivery',
                    'location' => 'Local Hub',
                    'description' => 'The rider is on the way to your location.',
                ],
            ];
        } elseif ($normalizedProvider === 'lbc') {
             $trackingHistory = [
                [
                    'date' => now()->subDays(2)->format('Y-m-d H:i:s'),
                    'status' => 'Accepted at LBC Branch',
                    'location' => 'Origin Branch',
                    'description' => 'Parcel has been accepted and is ready for consolidation.',
                ],
                [
                    'date' => now()->subDays(1)->format('Y-m-d H:i:s'),
                    'status' => 'In Transit to Destination',
                    'location' => 'Main Distribution Center',
                    'description' => 'Parcel is currently moving towards the destination province.',
                ],
                [
                    'date' => now()->format('Y-m-d H:i:s'),
                    'status' => 'Out for Delivery',
                    'location' => 'Delivery Team',
                    'description' => 'LBC team is out to deliver your package.',
                ],
            ];
        } else {
            $trackingHistory = [
                [
                    'date' => now()->subDay()->format('Y-m-d H:i:s'),
                    'status' => 'Order Processed',
                    'location' => 'Warehouse',
                    'description' => 'Your order has been processed and is awaiting pickup.',
                ],
                [
                    'date' => now()->format('Y-m-d H:i:s'),
                    'status' => 'In Transit',
                    'location' => 'Shipping Facility',
                    'description' => 'Your package is on its way.',
                ],
            ];
        }

        return [
            'tracking_number' => $trackingNumber,
            'provider' => $providerLabel,
            'status' => 'In Transit',
            'estimated_delivery' => $estimatedDelivery,
            'tracking_url' => $trackingUrl,
            'tracking_history' => $trackingHistory,
        ];
    }

    private function normalizeTrackingProvider(?string $provider): string
    {
        $value = strtolower(trim((string) $provider));

        if (in_array($value, ['j&t', 'jnt', 'j&t express', 'jnt express'], true)) {
            return 'jnt';
        }

        if (in_array($value, ['lbc', 'lbc express'], true)) {
            return 'lbc';
        }

        return 'unknown';
    }

    private function getTrackingProviderLabel(string $provider): string
    {
        if ($provider === 'jnt') {
            return 'J&T Express';
        }

        if ($provider === 'lbc') {
            return 'LBC Express';
        }

        return 'Unknown Courier';
    }

    private function buildTrackingUrl(string $provider, string $trackingNumber): ?string
    {
        if ($provider === 'jnt') {
            return 'https://www.jtexpress.ph/index/query/gzquery.html?billcode=' . urlencode($trackingNumber);
        }

        if ($provider === 'lbc') {
            return 'https://www.lbcexpress.com/track/?trackingNo=' . urlencode($trackingNumber);
        }

        return null;
    }
}
