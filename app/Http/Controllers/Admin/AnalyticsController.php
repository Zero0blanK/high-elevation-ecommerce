<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        return view('admin.analytics.index');
    }

    public function salesReport(Request $request)
    {
        $report = null;

        if ($request->filled(['period_start', 'period_end'])) {
            $request->validate([
                'period_start' => 'required|date',
                'period_end' => 'required|date|after_or_equal:period_start',
            ]);

            try {
                $report = $this->analyticsService->generateSalesReport(
                    $request->period_start,
                    $request->period_end
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Error generating sales report: ' . $e->getMessage());
            }
        }

        return view('admin.analytics.sales', compact('report'));
    }

    public function customerReport(Request $request)
    {
        $report = null;

        if ($request->filled(['period_start', 'period_end'])) {
            $request->validate([
                'period_start' => 'required|date',
                'period_end' => 'required|date|after_or_equal:period_start',
            ]);

            try {
                $report = $this->analyticsService->generateCustomerReport(
                    $request->period_start,
                    $request->period_end
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Error generating customer report: ' . $e->getMessage());
            }
        }

        return view('admin.analytics.customers', compact('report'));
    }

    public function inventoryReport()
    {
        try {
            $report = $this->analyticsService->generateInventoryReport();
            return view('admin.analytics.inventory', compact('report'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating inventory report: ' . $e->getMessage());
        }
    }

    public function exportReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sales,customers,inventory',
            'format' => 'required|in:csv',
            'period_start' => 'required_unless:type,inventory|date',
            'period_end' => 'required_unless:type,inventory|date',
        ]);

        try {
            switch ($request->type) {
                case 'sales':
                    $report = $this->analyticsService->generateSalesReport(
                        $request->period_start,
                        $request->period_end
                    );
                    break;
                case 'customers':
                    $report = $this->analyticsService->generateCustomerReport(
                        $request->period_start,
                        $request->period_end
                    );
                    break;
                case 'inventory':
                    $report = $this->analyticsService->generateInventoryReport();
                    break;
            }

            return $this->exportToCsv($report);
        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    private function exportToCsv($report)
    {
        $type = $report['type'];
        $data = $report['data'];
        $filename = $type . '_report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($type, $data) {
            $handle = fopen('php://output', 'w');

            switch ($type) {
                case 'sales':
                    fputcsv($handle, ['Sales Report']);
                    fputcsv($handle, ['Total Revenue', '$' . number_format($data['total_revenue'] ?? 0, 2)]);
                    fputcsv($handle, ['Total Orders', $data['total_orders'] ?? 0]);
                    fputcsv($handle, ['Average Order Value', '$' . number_format($data['average_order_value'] ?? 0, 2)]);
                    fputcsv($handle, []);
                    fputcsv($handle, ['Date', 'Revenue', 'Orders']);
                    foreach ($data['daily_sales'] ?? [] as $day) {
                        fputcsv($handle, [
                            $day['date'] ?? '',
                            '$' . number_format($day['total'] ?? 0, 2),
                            $day['orders'] ?? 0,
                        ]);
                    }
                    fputcsv($handle, []);
                    fputcsv($handle, ['Top Products', 'Units Sold', 'Revenue']);
                    foreach ($data['top_products'] ?? [] as $product) {
                        fputcsv($handle, [
                            $product['name'] ?? '',
                            $product['total_sold'] ?? 0,
                            '$' . number_format($product['revenue'] ?? 0, 2),
                        ]);
                    }
                    break;

                case 'customers':
                    fputcsv($handle, ['Customer Report']);
                    fputcsv($handle, ['New Customers', $data['new_customers'] ?? 0]);
                    fputcsv($handle, ['Returning Customers', $data['returning_customers'] ?? 0]);
                    fputcsv($handle, ['Avg Lifetime Value', '$' . number_format($data['customer_lifetime_value'] ?? 0, 2)]);
                    fputcsv($handle, []);
                    fputcsv($handle, ['Customer', 'Email', 'Total Spent', 'Orders']);
                    foreach ($data['top_customers'] ?? [] as $customer) {
                        fputcsv($handle, [
                            ($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''),
                            $customer['email'] ?? '',
                            '$' . number_format($customer['total_spent'] ?? 0, 2),
                            $customer['order_count'] ?? 0,
                        ]);
                    }
                    break;

                case 'inventory':
                    fputcsv($handle, ['Inventory Report']);
                    fputcsv($handle, ['Total Products', $data['total_products'] ?? 0]);
                    fputcsv($handle, ['Total Stock', $data['total_stock'] ?? 0]);
                    fputcsv($handle, ['Inventory Value', '$' . number_format($data['inventory_value'] ?? 0, 2)]);
                    fputcsv($handle, ['Out of Stock', $data['out_of_stock_count'] ?? 0]);
                    fputcsv($handle, []);
                    fputcsv($handle, ['Category', 'Products', 'Stock', 'Value']);
                    foreach ($data['category_breakdown'] ?? [] as $cat) {
                        fputcsv($handle, [
                            $cat['name'] ?? '',
                            $cat['product_count'] ?? 0,
                            $cat['total_stock'] ?? 0,
                            '$' . number_format($cat['stock_value'] ?? 0, 2),
                        ]);
                    }
                    break;
            }

            fclose($handle);
        }, 200, $headers);
    }
}