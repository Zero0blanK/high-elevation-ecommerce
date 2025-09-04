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
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start'
        ]);

        try {
            $report = $this->analyticsService->generateSalesReport(
                $request->period_start,
                $request->period_end
            );

            return view('admin.analytics.sales', compact('report'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating sales report: ' . $e->getMessage());
        }
    }

    public function customerReport(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start'
        ]);

        try {
            $report = $this->analyticsService->generateCustomerReport(
                $request->period_start,
                $request->period_end
            );

            return view('admin.analytics.customers', compact('report'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating customer report: ' . $e->getMessage());
        }
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
            'format' => 'required|in:pdf,excel,csv',
            'period_start' => 'required_unless:type,inventory|date',
            'period_end' => 'required_unless:type,inventory|date'
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

            return $this->downloadReport($report, $request->format);
        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting report: ' . $e->getMessage());
        }
    }

    private function downloadReport($report, $format)
    {
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($report);
            case 'excel':
                return $this->exportToExcel($report);
            case 'csv':
                return $this->exportToCsv($report);
        }
    }

    private function exportToPdf($report)
    {
        // Implement PDF export using libraries like DomPDF or TCPDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.analytics.pdf', compact('report'));
        return $pdf->download($report->type . '_report_' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportToExcel($report)
    {
        // Implement Excel export using Laravel Excel
        return \Excel::download(new \App\Exports\AnalyticsReportExport($report), 
            $report->type . '_report_' . now()->format('Y-m-d') . '.xlsx');
    }

    private function exportToCsv($report)
    {
        // Implement CSV export
        $filename = $report->type . '_report_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($report) {
            $handle = fopen('php://output', 'w');
            
            // Write CSV headers and data based on report type
            $this->writeCsvData($handle, $report);
            
            fclose($handle);
        }, 200, $headers);
    }

    private function writeCsvData($handle, $report)
    {
        switch ($report->type) {
            case 'sales':
                fputcsv($handle, ['Date', 'Revenue', 'Orders', 'Average Order Value']);
                foreach ($report->data['daily_sales'] as $day) {
                    fputcsv($handle, [$day['date'], $day['total'], $day['orders'], 
                        $day['orders'] > 0 ? $day['total'] / $day['orders'] : 0]);
                }
                break;
            // Add other report types as needed
        }
    }
}