<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\AnalyticsReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function generateSalesReport($periodStart, $periodEnd)
    {
        $orders = Order::whereBetween('created_at', [$periodStart, $periodEnd])
            ->where('payment_status', 'paid');

        $data = [
            'total_revenue' => $orders->sum('total_amount'),
            'total_orders' => $orders->count(),
            'average_order_value' => $orders->avg('total_amount'),
            'daily_sales' => $this->getDailySales($periodStart, $periodEnd),
            'top_products' => $this->getTopProducts($periodStart, $periodEnd),
            'sales_by_category' => $this->getSalesByCategory($periodStart, $periodEnd),
        ];

        return $this->saveReport('sales', 'Sales Report', $periodStart, $periodEnd, $data);
    }

    public function generateCustomerReport($periodStart, $periodEnd)
    {
        $data = [
            'new_customers' => Customer::whereBetween('created_at', [$periodStart, $periodEnd])->count(),
            'returning_customers' => $this->getReturningCustomers($periodStart, $periodEnd),
            'customer_lifetime_value' => $this->getAverageCustomerLifetimeValue(),
            'top_customers' => $this->getTopCustomers($periodStart, $periodEnd),
            'customer_acquisition_by_day' => $this->getCustomerAcquisitionByDay($periodStart, $periodEnd),
        ];

        return $this->saveReport('customers', 'Customer Report', $periodStart, $periodEnd, $data);
    }

    public function generateInventoryReport()
    {
        $data = [
            'low_stock_products' => app(InventoryService::class)->getLowStockProducts(),
            'out_of_stock_products' => Product::where('stock_quantity', 0)->where('is_active', true)->count(),
            'total_products' => Product::where('is_active', true)->count(),
            'inventory_value' => Product::where('is_active', true)->sum(DB::raw('price * stock_quantity')),
            'top_selling_products' => $this->getTopSellingProducts(),
        ];

        return $this->saveReport('inventory', 'Inventory Report', now()->startOfMonth(), now(), $data);
    }

    public function getDashboardMetrics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->startOfMonth()->subDay();

        return [
            'today_sales' => Order::whereDate('created_at', $today)->where('payment_status', 'paid')->sum('total_amount'),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'month_sales' => Order::where('created_at', '>=', $thisMonth)->where('payment_status', 'paid')->sum('total_amount'),
            'month_orders' => Order::where('created_at', '>=', $thisMonth)->count(),
            'last_month_sales' => Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'low_stock_count' => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count(),
            'total_customers' => Customer::count(),
            'recent_orders' => Order::with('customer')->latest()->limit(10)->get(),
        ];
    }

    private function getDailySales($periodStart, $periodEnd)
    {
        return Order::whereBetween('created_at', [$periodStart, $periodEnd])
            ->where('payment_status', 'paid')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getTopProducts($periodStart, $periodEnd, $limit = 10)
    {
        return Product::select('products.name', 'products.sku', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.total_price) as revenue'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$periodStart, $periodEnd])
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getSalesByCategory($periodStart, $periodEnd)
    {
        return DB::table('categories')
            ->select('categories.name', DB::raw('SUM(order_items.total_price) as revenue'))
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$periodStart, $periodEnd])
            ->where('orders.payment_status', 'paid')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('revenue', 'desc')
            ->get();
    }

    private function saveReport($type, $title, $periodStart, $periodEnd, $data)
    {
        return AnalyticsReport::create([
            'type' => $type,
            'title' => $title,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'data' => $data,
            'generated_by' => auth('admin')->id()
        ]);
    }
}