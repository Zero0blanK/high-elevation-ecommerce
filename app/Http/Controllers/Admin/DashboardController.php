<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        // Get current month and last month dates
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        // Fetch metrics
        $metrics = [
            'total_sales' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'total_orders' => Order::count(),
            'total_customers' => Customer::count(),
            'total_products' => Product::count(),
            'month_sales' => Order::where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount'),
            'last_month_sales' => Order::where('payment_status', 'paid')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->sum('total_amount'),
        ];

        // Get recent activity
        $recentActivity = collect();

        // Recent orders
        $recentOrders = Order::with('customer')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($order) {
                return [
                    'type' => 'order',
                    'title' => "New order #{$order->order_number}",
                    'description' => "Order placed by {$order->customer->full_name}",
                    'amount' => $order->total_amount,
                    'time' => $order->created_at,
                    'status' => $order->status
                ];
            });

        // Recent customers
        $recentCustomers = Customer::latest()
            ->take(3)
            ->get()
            ->map(function($customer) {
                return [
                    'type' => 'customer',
                    'title' => 'New customer registered',
                    'description' => "{$customer->full_name} joined",
                    'time' => $customer->created_at
                ];
            });

        $recentActivity = $recentOrders->concat($recentCustomers)
            ->sortByDesc('time')
            ->take(8);

        // Get low stock products
        $lowStockProducts = Product::where('stock_quantity', '<=', DB::raw('low_stock_threshold'))
            ->where('is_active', true)
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();

        // Get top selling products
        $topSellingProducts = Product::withCount(['orderItems as total_sold' => function($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'metrics',
            'recentActivity',
            'lowStockProducts',
            'topSellingProducts'
        ));
    }

    public function getChartData($type, $period = '7days')
    {
        switch ($type) {
            case 'sales':
                return $this->getSalesChartData($period);
            case 'orders':
                return $this->getOrdersChartData($period);
            case 'customers':
                return $this->getCustomersChartData($period);
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    private function getSalesChartData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        $startDate = Carbon::now()->subDays($days);

        $data = \App\Models\Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('created_at', '>=', $startDate)
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date'),
            'datasets' => [
                'label' => 'Sales',
                'data' => $data->pluck('total'),
                'borderColor' => 'rgb(59, 130, 246)',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
            ]
        ]);
    }

    private function getOrdersChartData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        $startDate = Carbon::now()->subDays($days);

        $data = \App\Models\Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date'),
            'datasets' => [
                'label' => 'Orders',
                'data' => $data->pluck('count'),
                'borderColor' => 'rgb(16, 185, 129)',
                'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
            ]
        ]);
    }

    private function calculateOrdersGrowth($thisMonth, $lastMonth, $lastMonthEnd)
    {
        $thisMonthOrders = \App\Models\Order::where('created_at', '>=', $thisMonth)->count();
        $lastMonthOrders = \App\Models\Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        
        return $this->calculateGrowthRate($thisMonthOrders, $lastMonthOrders);
    }

    private function calculateGrowthRate($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function getDaysFromPeriod($period)
    {
        switch ($period) {
            case '7days': return 7;
            case '30days': return 30;
            case '90days': return 90;
            default: return 7;
        }
    }
    
    private function getCustomersChartData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        $startDate = Carbon::now()->subDays($days);

        $data = \App\Models\Customer::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date'),
            'datasets' => [
                'label' => 'New Customers',
                'data' => $data->pluck('count'),
                'borderColor' => 'rgb(245, 158, 11)',
                'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
            ]
        ]);
    }

    public function getRecentActivity()
    {
        $recentOrders = \App\Models\Order::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $recentCustomers = \App\Models\Customer::latest()
            ->take(3)
            ->get();

        $activities = collect();

        foreach ($recentOrders as $order) {
            $activities->push([
                'type' => 'order',
                'title' => "New order #{$order->order_number}",
                'description' => "Order placed by {$order->customer->name}",
                'amount' => $order->total_amount,
                'time' => $order->created_at,
                'icon' => 'mdi-package-variant',
                'color' => 'primary'
            ]);
        }

        foreach ($recentCustomers as $customer) {
            $activities->push([
                'type' => 'customer',
                'title' => "New customer registered",
                'description' => $customer->name . ' joined',
                'time' => $customer->created_at,
                'icon' => 'mdi-account-plus',
                'color' => 'success'
            ]);
        }

        return $activities->sortByDesc('time')->take(8)->values();
    }

    public function getLowStockProducts()
    {
        return \App\Models\Product::where('stock_quantity', '<=', 10)
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity', 'asc')
            ->take(5)
            ->get();
    }

    public function getTopSellingProducts()
    {
        return \App\Models\Product::withCount(['orderItems as total_sold' => function($query) {
                $query->select(\DB::raw('SUM(quantity)'));
            }])
            ->having('total_sold', '>', 0)
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();
    }

    public function getSalesData(Request $request)
    {
        $period = $request->get('period', '7days');
        $days = match($period) {
            '30days' => 30,
            '90days' => 90,
            default => 7
        };

        $sales = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $sales->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d')),
            'datasets' => [
                [
                    'data' => $sales->pluck('total')
                ]
            ]
        ]);
    }

    public function getOrderStatusData()
    {
        $statuses = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        return response()->json([
            'labels' => ['Completed ✅', 'Processing ⏳', 'Shipped 🚚', 'Pending ⏱️', 'Cancelled ❌'],
            'data' => [
                $statuses['completed'] ?? 0,
                $statuses['processing'] ?? 0,
                $statuses['shipped'] ?? 0,
                $statuses['pending'] ?? 0,
                $statuses['cancelled'] ?? 0
            ]
        ]);
    }
}