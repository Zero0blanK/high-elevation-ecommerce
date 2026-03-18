<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $timeframe = $request->get('timeframe', 'today');
        $dateRange = $this->getDateRange($timeframe);
        
        $kpis = $this->getKpiData($dateRange['start'], $dateRange['end']);
        
        return view('admin.kpi.index', compact('kpis', 'timeframe'));
    }

    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'sales');
        $period = $request->get('period', '7days');
        
        switch ($type) {
            case 'sales':
                return $this->getSalesChartData($period);
            case 'orders':
                return $this->getOrdersChartData($period);
            case 'customers':
                return $this->getCustomersChartData($period);
            case 'order-status':
                return $this->getOrderStatusChartData();
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    private function getSalesChartData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        $startDate = Carbon::now()->subDays($days);
        
        $data = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('created_at', '>=', $startDate)
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing dates with 0 values
        $dateRange = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $dateRange->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        $filledData = $dateRange->map(function ($date) use ($data) {
            $dayData = $data->firstWhere('date', $date);
            return [
                'date' => Carbon::parse($date)->format('M d'),
                'total' => $dayData ? floatval($dayData->total) : 0
            ];
        });

        return response()->json([
            'labels' => $filledData->pluck('date'),
            'datasets' => [
                'data' => $filledData->pluck('total'),
                'borderColor' => 'rgb(59, 130, 246)',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'label' => 'Sales Revenue'
            ]
        ]);
    }

    private function getOrdersChartData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        $startDate = Carbon::now()->subDays($days);

        $data = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing dates with 0 values
        $dateRange = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $dateRange->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        $filledData = $dateRange->map(function ($date) use ($data) {
            $dayData = $data->firstWhere('date', $date);
            return [
                'date' => Carbon::parse($date)->format('M d'),
                'count' => $dayData ? intval($dayData->count) : 0
            ];
        });

        return response()->json([
            'labels' => $filledData->pluck('date'),
            'datasets' => [
                'data' => $filledData->pluck('count'),
                'borderColor' => 'rgb(16, 185, 129)',
                'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                'label' => 'Orders'
            ]
        ]);
    }

    private function getCustomersChartData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        $startDate = Carbon::now()->subDays($days);

        $data = Customer::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing dates with 0 values
        $dateRange = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $dateRange->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        $filledData = $dateRange->map(function ($date) use ($data) {
            $dayData = $data->firstWhere('date', $date);
            return [
                'date' => Carbon::parse($date)->format('M d'),
                'count' => $dayData ? intval($dayData->count) : 0
            ];
        });

        return response()->json([
            'labels' => $filledData->pluck('date'),
            'datasets' => [
                'data' => $filledData->pluck('count'),
                'borderColor' => 'rgb(245, 158, 11)',
                'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                'label' => 'New Customers'
            ]
        ]);
    }

    private function getOrderStatusChartData()
    {
        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $statuses = [
            'completed' => $statusCounts->get('completed', 0),
            'processing' => $statusCounts->get('processing', 0),
            'shipped' => $statusCounts->get('shipped', 0),
            'pending' => $statusCounts->get('pending', 0),
            'cancelled' => $statusCounts->get('cancelled', 0),
        ];

        return response()->json([
            'labels' => ['Completed', 'Processing', 'Shipped', 'Pending', 'Cancelled'],
            'datasets' => [
                'data' => array_values($statuses),
                'backgroundColor' => [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(156, 163, 175, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                'borderColor' => [
                    'rgba(16, 185, 129, 1)',
                    'rgba(59, 130, 246, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(156, 163, 175, 1)',
                    'rgba(239, 68, 68, 1)'
                ]
            ]
        ]);
    }

    private function getDaysFromPeriod($period)
    {
        return match($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            default => 7
        };
    }

    private function getDateRange($timeframe)
    {
        $now = Carbon::now();
        
        return match($timeframe) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'yesterday' => [
                'start' => $now->copy()->subDay()->startOfDay(),
                'end' => $now->copy()->subDay()->endOfDay(),
            ],
            'this_week' => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
            ],
            'last_week' => [
                'start' => $now->copy()->subWeek()->startOfWeek(),
                'end' => $now->copy()->subWeek()->endOfWeek(),
            ],
            'this_month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
            'last_month' => [
                'start' => $now->copy()->subMonth()->startOfMonth(),
                'end' => $now->copy()->subMonth()->endOfMonth(),
            ],
            'this_year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
            ],
            default => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
        };
    }

    private function getKpiData($startDate, $endDate)
    {
        return [
            'sales' => [
                'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])->where('payment_status', 'paid')->sum('total_amount'),
                'average_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])->where('payment_status', 'paid')->avg('total_amount') ?? 0,
                'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
                'completed_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
                'cancelled_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'cancelled')->count(),
            ],
            'customers' => [
                'new_customers' => Customer::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_customers' => Customer::count(),
            ],
            'products' => [
                'total_products' => Product::count(),
                'low_stock_products' => Product::where('stock_quantity', '<=', DB::raw('COALESCE(low_stock_threshold, 10)'))->count(),
                'out_of_stock' => Product::where('stock_quantity', 0)->count(),
                'top_selling' => $this->getTopSellingProducts($startDate, $endDate),
                'performance' => $this->getProductPerformanceMetrics($startDate, $endDate),
            ],
            'inventory' => [
                'total_inventory_value' => Product::sum(DB::raw('stock_quantity * price')),
                'average_product_value' => Product::avg('price') ?? 0,
            ],
        ];
    }
    private function getProductPerformanceMetrics($startDate, $endDate)
    {
        return Product::select([
            'products.id',
            'products.name',
            'products.stock_quantity',
            'products.low_stock_threshold',
            'products.price',
            'products.cost_price',
            DB::raw('COALESCE(SUM(order_items.quantity), 0) as units_sold'),
            DB::raw('COALESCE(SUM(order_items.total_price), 0) as revenue'),
        ])
        ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->leftJoin('orders', function($join) use ($startDate, $endDate) {
            $join->on('order_items.order_id', '=', 'orders.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate]);
        })
        ->groupBy([
            'products.id',
            'products.name',
            'products.stock_quantity',
            'products.low_stock_threshold',
            'products.price',
            'products.cost_price'
        ])
        ->orderByDesc('revenue')
        ->take(10)
        ->get()
        ->map(function($product) {
            $sellingPrice = (float) $product->price;
            $costPrice = (float) ($product->cost_price ?? 0);

            // Profit margin = ((Selling Price - Cost) / Selling Price) * 100
            $profit_margin = ($sellingPrice > 0 && $costPrice > 0)
                ? (($sellingPrice - $costPrice) / $sellingPrice) * 100
                : 0;
                
            return [
                'name' => $product->name,
                'revenue' => $product->revenue,
                'units_sold' => $product->units_sold,
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
                'profit_margin' => round($profit_margin, 1),
                'stock_quantity' => $product->stock_quantity,
                'stock_threshold' => $product->low_stock_threshold ?? 10
            ];
        });
    }

    private function getTopSellingProducts($startDate, $endDate)
    {
        return Product::select([
                'products.id',
                'products.name',
                'products.sku',
                'products.price',
                'products.stock_quantity',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ])
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startDate, $endDate]);
            })
            ->whereNull('products.deleted_at')
            ->groupBy([
                'products.id',
                'products.name',
                'products.sku',
                'products.price',
                'products.stock_quantity'
            ])
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();
    }

    public function export(Request $request)
    {
        $timeframe = $request->get('timeframe', 'today');
        $dateRange = $this->getDateRange($timeframe);
        $kpis = $this->getKpiData($dateRange['start'], $dateRange['end']);

        $filename = 'kpi_analytics_' . $timeframe . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($kpis, $timeframe, $dateRange) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['KPI Analytics Report']);
            fputcsv($handle, ['Period', ucfirst(str_replace('_', ' ', $timeframe))]);
            fputcsv($handle, ['Generated', now()->format('F j, Y g:i A')]);
            fputcsv($handle, ['Date Range', $dateRange['start']->format('M j, Y') . ' - ' . $dateRange['end']->format('M j, Y')]);
            fputcsv($handle, []);

            fputcsv($handle, ['SALES METRICS']);
            fputcsv($handle, ['Total Revenue', '$' . number_format($kpis['sales']['total_revenue'], 2)]);
            fputcsv($handle, ['Average Order Value', '$' . number_format($kpis['sales']['average_order_value'], 2)]);
            fputcsv($handle, ['Total Orders', $kpis['sales']['total_orders']]);
            fputcsv($handle, ['Completed Orders', $kpis['sales']['completed_orders']]);
            fputcsv($handle, ['Cancelled Orders', $kpis['sales']['cancelled_orders']]);
            fputcsv($handle, []);

            fputcsv($handle, ['CUSTOMER METRICS']);
            fputcsv($handle, ['New Customers', $kpis['customers']['new_customers']]);
            fputcsv($handle, ['Total Customers', $kpis['customers']['total_customers']]);
            fputcsv($handle, []);

            fputcsv($handle, ['PRODUCT METRICS']);
            fputcsv($handle, ['Total Products', $kpis['products']['total_products']]);
            fputcsv($handle, ['Low Stock Products', $kpis['products']['low_stock_products']]);
            fputcsv($handle, ['Out of Stock', $kpis['products']['out_of_stock']]);
            fputcsv($handle, []);

            fputcsv($handle, ['TOP SELLING PRODUCTS']);
            fputcsv($handle, ['Product Name', 'Units Sold']);
            foreach ($kpis['products']['top_selling'] as $product) {
                fputcsv($handle, [$product->name, $product->total_sold ?? 0]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Get analytics summary for dashboard widgets
     */
    public function getAnalyticsSummary(Request $request)
    {
        $period = $request->get('period', 'week');
        $dateRange = $this->getDateRange($period === 'week' ? 'this_week' : 'this_month');

        $summary = [
            'revenue_growth' => $this->calculateRevenueGrowth($dateRange['start'], $dateRange['end']),
            'order_growth' => $this->calculateOrderGrowth($dateRange['start'], $dateRange['end']),
            'customer_growth' => $this->calculateCustomerGrowth($dateRange['start'], $dateRange['end']),
            'avg_order_growth' => $this->calculateAverageOrderGrowth($dateRange['start'], $dateRange['end']),
        ];

        return response()->json($summary);
    }

    private function calculateRevenueGrowth($startDate, $endDate)
    {
        $currentPeriod = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $previousStart = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $previousEnd = $startDate->copy()->subDay();

        $previousPeriod = Order::whereBetween('created_at', [$previousStart, $previousEnd])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        return $this->calculateGrowthPercentage($currentPeriod, $previousPeriod);
    }

    private function calculateOrderGrowth($startDate, $endDate)
    {
        $currentPeriod = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousStart = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $previousEnd = $startDate->copy()->subDay();
        $previousPeriod = Order::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        return $this->calculateGrowthPercentage($currentPeriod, $previousPeriod);
    }

    private function calculateCustomerGrowth($startDate, $endDate)
    {
        $currentPeriod = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousStart = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $previousEnd = $startDate->copy()->subDay();
        $previousPeriod = Customer::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        return $this->calculateGrowthPercentage($currentPeriod, $previousPeriod);
    }

    private function calculateAverageOrderGrowth($startDate, $endDate)
    {
        $currentPeriod = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->avg('total_amount') ?? 0;

        $previousStart = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $previousEnd = $startDate->copy()->subDay();

        $previousPeriod = Order::whereBetween('created_at', [$previousStart, $previousEnd])
            ->where('payment_status', 'paid')
            ->avg('total_amount') ?? 0;

        return $this->calculateGrowthPercentage($currentPeriod, $previousPeriod);
    }

    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}