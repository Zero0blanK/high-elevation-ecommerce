<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function generateSalesReport($periodStart, $periodEnd)
    {
        $orders = Order::whereBetween('created_at', [$periodStart, $periodEnd])
            ->where('payment_status', 'paid');

        return [
            'type' => 'sales',
            'title' => 'Sales Report',
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'data' => [
                'total_revenue' => (clone $orders)->sum('total_amount'),
                'total_orders' => (clone $orders)->count(),
                'average_order_value' => (clone $orders)->avg('total_amount') ?? 0,
                'daily_sales' => $this->getDailySales($periodStart, $periodEnd),
                'top_products' => $this->getTopProducts($periodStart, $periodEnd),
                'sales_by_category' => $this->getSalesByCategory($periodStart, $periodEnd),
            ],
        ];
    }

    public function generateCustomerReport($periodStart, $periodEnd)
    {
        return [
            'type' => 'customers',
            'title' => 'Customer Report',
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'data' => [
                'new_customers' => Customer::whereBetween('created_at', [$periodStart, $periodEnd])->count(),
                'returning_customers' => $this->getReturningCustomers($periodStart, $periodEnd),
                'customer_lifetime_value' => $this->getAverageCustomerLifetimeValue(),
                'top_customers' => $this->getTopCustomers($periodStart, $periodEnd),
                'customer_acquisition_by_day' => $this->getCustomerAcquisitionByDay($periodStart, $periodEnd),
            ],
        ];
    }

    public function generateInventoryReport()
    {
        return [
            'type' => 'inventory',
            'title' => 'Inventory Report',
            'period_start' => now()->startOfMonth(),
            'period_end' => now(),
            'data' => [
                'low_stock_products' => Product::where('is_active', true)
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->with('category')
                    ->get()
                    ->toArray(),
                'out_of_stock_count' => Product::where('stock_quantity', 0)->where('is_active', true)->count(),
                'total_products' => Product::where('is_active', true)->count(),
                'total_stock' => Product::where('is_active', true)->sum('stock_quantity'),
                'inventory_value' => Product::where('is_active', true)->sum(DB::raw('price * stock_quantity')),
                'top_selling_products' => $this->getTopSellingProducts(),
                'category_breakdown' => $this->getCategoryBreakdown(),
            ],
        ];
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
            ->get()
            ->toArray();
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
            ->get()
            ->toArray();
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
            ->get()
            ->map(fn ($item) => (array) $item)
            ->toArray();
    }

    private function getReturningCustomers($periodStart, $periodEnd)
    {
        return Customer::whereHas('orders', function ($q) use ($periodStart, $periodEnd) {
            $q->whereBetween('created_at', [$periodStart, $periodEnd]);
        })->whereHas('orders', function ($q) use ($periodStart) {
            $q->where('created_at', '<', $periodStart);
        })->count();
    }

    private function getAverageCustomerLifetimeValue()
    {
        $result = DB::query()
            ->selectRaw('AVG(customer_total) as avg_ltv')
            ->fromSub(function ($query) {
                $query->from('orders')
                    ->where('payment_status', 'paid')
                    ->selectRaw('customer_id, SUM(total_amount) as customer_total')
                    ->groupBy('customer_id');
            }, 'customer_totals')
            ->first();

        return $result->avg_ltv ?? 0;
    }

    private function getTopCustomers($periodStart, $periodEnd, $limit = 10)
    {
        return Customer::select('customers.id', 'customers.first_name', 'customers.last_name', 'customers.email')
            ->selectRaw('SUM(orders.total_amount) as total_spent, COUNT(orders.id) as order_count')
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->whereBetween('orders.created_at', [$periodStart, $periodEnd])
            ->where('orders.payment_status', 'paid')
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name', 'customers.email')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function getCustomerAcquisitionByDay($periodStart, $periodEnd)
    {
        return Customer::whereBetween('created_at', [$periodStart, $periodEnd])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getTopSellingProducts($limit = 10)
    {
        return Product::select('products.name', 'products.sku', 'products.stock_quantity', 'products.price')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->where('products.is_active', true)
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.stock_quantity', 'products.price')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function getCategoryBreakdown()
    {
        return DB::table('categories')
            ->select('categories.name')
            ->selectRaw('COUNT(products.id) as product_count')
            ->selectRaw('COALESCE(SUM(products.stock_quantity), 0) as total_stock')
            ->selectRaw('COALESCE(SUM(products.price * products.stock_quantity), 0) as stock_value')
            ->leftJoin('products', function ($join) {
                $join->on('categories.id', '=', 'products.category_id')
                     ->whereNull('products.deleted_at');
            })
            ->whereNull('categories.deleted_at')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('stock_value', 'desc')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->toArray();
    }
}