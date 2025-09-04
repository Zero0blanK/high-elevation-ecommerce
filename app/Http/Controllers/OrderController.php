<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        
        // Get order counts for tabs
        $orderCounts = [
            'all' => $customer->orders()->count(),
            'pending' => $customer->orders()->where('status', 'pending')->count(),
            'processing' => $customer->orders()->where('status', 'processing')->count(),
            'shipped' => $customer->orders()->where('status', 'shipped')->count(),
            'delivered' => $customer->orders()->where('status', 'delivered')->count(),
            'cancelled' => $customer->orders()->where('status', 'cancelled')->count(),
        ];

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

        // Get tracking data if order is shipped and has tracking number
        $trackingData = null;
        if ($order->status === 'shipped' && $order->tracking_number) {
            // For now, let's use mock data to test the display
            $trackingData = [
                'status' => 'In Transit',
                'estimated_delivery' => 'Dec 25, 2024',
                'tracking_history' => [
                    [
                        'location' => 'Manila Distribution Center',
                        'date' => '2024-12-20 14:30:00',
                        'status' => 'Package is out for delivery',
                        'note' => 'Your package is on the delivery truck'
                    ],
                    [
                        'location' => 'Quezon City Hub',
                        'date' => '2024-12-20 08:15:00',
                        'status' => 'Package arrived at sorting facility',
                        'note' => 'Package is being sorted for delivery'
                    ],
                    [
                        'location' => 'Cebu Processing Center',
                        'date' => '2024-12-19 16:45:00',
                        'status' => 'Package in transit',
                        'note' => 'Package is on its way to destination city'
                    ],
                    [
                        'location' => 'Origin Facility - Davao',
                        'date' => '2024-12-19 09:00:00',
                        'status' => 'Package picked up',
                        'note' => 'Package has been collected from sender'
                    ]
                ]
            ];
            
            // Later, replace this with actual API call:
            // $trackingData = $this->getTrackingData($order->tracking_number, $order->shipping_method ?? 'standard');
        }

        return view('account.order-details', compact('order', 'trackingData'));
    }

    public function showTrackingForm()
    {
        return view('order.track');
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

        // Only allow cancellation for pending or processing orders
        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->withErrors(['error' => 'This order cannot be cancelled.']);
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order has been cancelled successfully.');
    }

    public function confirmReceived(Order $order)
    {
        $customer = Auth::guard('customer')->user();

        // Ensure the order belongs to the authenticated customer
        if ($order->customer_id !== $customer->id) {
            abort(403);
        }

        // Only allow confirmation for shipped orders
        if ($order->status !== 'shipped') {
            return back()->withErrors(['error' => 'This order cannot be marked as received.']);
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        return back()->with('success', 'Order marked as received successfully.');
    }

    public function trackPackage(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
            'provider' => 'required|string'
        ]);

        // This will be expanded when integrating with LBC, J&T APIs
        $trackingData = $this->getTrackingData($request->tracking_number, $request->provider);

        return response()->json($trackingData);
    }

    private function getTrackingData($trackingNumber, $provider)
    {
        // Placeholder for tracking API integration
        // This will be replaced with actual API calls to LBC, J&T, etc.
        
        $mockData = [
            'tracking_number' => $trackingNumber,
            'provider' => $provider,
            'status' => 'In Transit',
            'estimated_delivery' => now()->addDays(2)->format('Y-m-d'),
            'tracking_history' => [
                [
                    'date' => now()->subDays(2)->format('Y-m-d H:i:s'),
                    'status' => 'Package picked up',
                    'location' => 'Origin Hub',
                    'description' => 'Package has been picked up from sender'
                ],
                [
                    'date' => now()->subDays(1)->format('Y-m-d H:i:s'),
                    'status' => 'In transit',
                    'location' => 'Sorting Facility',
                    'description' => 'Package is being processed at sorting facility'
                ],
                [
                    'date' => now()->format('Y-m-d H:i:s'),
                    'status' => 'Out for delivery',
                    'location' => 'Local Hub',
                    'description' => 'Package is out for delivery'
                ]
            ]
        ];

        return $mockData;
    }
}