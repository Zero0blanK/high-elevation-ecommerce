<?php

namespace App\Console\Commands;

use App\Services\AnalyticsService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateSalesReports extends Command
{
    protected $signature = 'reports:sales {type=daily : Report type: daily, weekly, monthly}';
    protected $description = 'Generate automated sales reports';

    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        parent::__construct();
        $this->analyticsService = $analyticsService;
    }

    public function handle()
    {
        $type = $this->argument('type');

        switch ($type) {
            case 'daily':
                $this->generateDailyReport();
                break;
            case 'weekly':
                $this->generateWeeklyReport();
                break;
            case 'monthly':
                $this->generateMonthlyReport();
                break;
            default:
                $this->error('Invalid report type. Use: daily, weekly, or monthly');
                return 1;
        }

        return 0;
    }

    private function generateDailyReport()
    {
        $this->info('Generating daily sales report...');

        $yesterday = Carbon::yesterday();
        
        $report = $this->analyticsService->generateSalesReport(
            $yesterday->startOfDay(),
            $yesterday->endOfDay()
        );

        $this->info("Daily report generated for {$yesterday->toDateString()}");
        $this->line("Total Revenue: $" . number_format($report->data['total_revenue'], 2));
        $this->line("Total Orders: " . $report->data['total_orders']);
    }

    private function generateWeeklyReport()
    {
        $this->info('Generating weekly sales report...');

        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        
        $report = $this->analyticsService->generateSalesReport($lastWeekStart, $lastWeekEnd);

        $this->info("Weekly report generated for week of {$lastWeekStart->toDateString()}");
        $this->line("Total Revenue: $" . number_format($report->data['total_revenue'], 2));
        $this->line("Total Orders: " . $report->data['total_orders']);
    }

    private function generateMonthlyReport()
    {
        $this->info('Generating monthly sales report...');

        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        $report = $this->analyticsService->generateSalesReport($lastMonthStart, $lastMonthEnd);

        $this->info("Monthly report generated for {$lastMonthStart->format('F Y')}");
        $this->line("Total Revenue: $" . number_format($report->data['total_revenue'], 2));
        $this->line("Total Orders: " . $report->data['total_orders']);

        // Send monthly report to administrators
        \Mail::to(config('ecommerce.emails.new_order.recipients'))
            ->send(new \App\Mail\MonthlySalesReport($report));
    }
}