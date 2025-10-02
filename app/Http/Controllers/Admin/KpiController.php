<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $orders = Order::whereBetween('created_at', [$startDate, $endDate]);
        $customers = Customer::whereBetween('created_at', [$startDate, $endDate]);
        
        return [
            'sales' => [
                'total_revenue' => $orders->where('payment_status', 'paid')->sum('total_amount'),
                'average_order_value' => $orders->where('payment_status', 'paid')->avg('total_amount') ?? 0,
                'total_orders' => $orders->count(),
                'completed_orders' => $orders->where('status', 'completed')->count(),
                'cancelled_orders' => $orders->where('status', 'cancelled')->count(),
            ],
            'customers' => [
                'new_customers' => $customers->count(),
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
            DB::raw('COALESCE(SUM(order_items.quantity), 0) as units_sold'),
            DB::raw('COALESCE(SUM(order_items.total_price), 0) as revenue'),
            DB::raw('COALESCE(AVG(
                CASE 
                    WHEN orders.payment_status = "paid" 
                    THEN order_items.total_price 
                END), 0) as avg_price')
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
            'products.price'
        ])
        ->orderByDesc('revenue')
        ->take(10)
        ->get()
        ->map(function($product) {
            $profit_margin = $product->price > 0 
                ? (($product->avg_price - $product->price) / $product->price) * 100 
                : 0;
                
            return [
                'name' => $product->name,
                'revenue' => $product->revenue,
                'units_sold' => $product->units_sold,
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title and headers
        $sheet->setCellValue('A1', 'KPI Analytics Report');
        $sheet->setCellValue('A2', 'Period: ' . ucfirst(str_replace('_', ' ', $timeframe)));
        $sheet->setCellValue('A3', 'Generated: ' . now()->format('F j, Y \a\t g:i A'));
        $sheet->setCellValue('A4', 'Date Range: ' . $dateRange['start']->format('M j, Y') . ' - ' . $dateRange['end']->format('M j, Y'));

        // Sales Metrics Section
        $row = 6;
        $sheet->setCellValue('A' . $row, 'SALES METRICS');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $row++;

        $salesMetrics = [
            'Total Revenue' => number_format($kpis['sales']['total_revenue'], 2),
            'Average Order Value' => number_format($kpis['sales']['average_order_value'], 2),
            'Total Orders' => number_format($kpis['sales']['total_orders']),
            'Completed Orders' => number_format($kpis['sales']['completed_orders']),
            'Cancelled Orders' => number_format($kpis['sales']['cancelled_orders']),
        ];

        foreach ($salesMetrics as $metric => $value) {
            $sheet->setCellValue('A' . $row, $metric);
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }

        // Customer Metrics Section
        $row++;
        $sheet->setCellValue('A' . $row, 'CUSTOMER METRICS');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $row++;

        $customerMetrics = [
            'New Customers' => number_format($kpis['customers']['new_customers']),
            'Total Customers' => number_format($kpis['customers']['total_customers']),
        ];

        foreach ($customerMetrics as $metric => $value) {
            $sheet->setCellValue('A' . $row, $metric);
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }

        // Product Metrics Section
        $row++;
        $sheet->setCellValue('A' . $row, 'PRODUCT METRICS');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $row++;

        $productMetrics = [
            'Total Products' => number_format($kpis['products']['total_products']),
            'Low Stock Products' => number_format($kpis['products']['low_stock_products']),
            'Out of Stock Products' => number_format($kpis['products']['out_of_stock']),
        ];

        foreach ($productMetrics as $metric => $value) {
            $sheet->setCellValue('A' . $row, $metric);
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }

        // Top Selling Products
        $row++;
        $sheet->setCellValue('A' . $row, 'TOP SELLING PRODUCTS');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $row++;

        $sheet->setCellValue('A' . $row, 'Product Name');
        $sheet->setCellValue('B' . $row, 'Units Sold');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $row++;

        foreach ($kpis['products']['top_selling'] as $product) {
            $sheet->setCellValue('A' . $row, $product->name);
            $sheet->setCellValue('B' . $row, $product->total_sold ?? 0);
            $row++;
        }

        // Format the spreadsheet
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        
        // Style the header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A4')->getFont()->setItalic(true);

        // Add borders to data sections
        $lastRow = $row - 1;
        $sheet->getStyle('A6:B' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Create and download the file
        $writer = new Xlsx($spreadsheet);
        $filename = 'kpi_analytics_' . $timeframe . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
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