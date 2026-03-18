<?php

namespace App\Jobs;

use App\Services\AnalyticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAnalyticsReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $reportType = 'sales',
        protected ?string $periodStart = null,
        protected ?string $periodEnd = null
    ) {}

    public function handle(AnalyticsService $analyticsService): void
    {
        $start = $this->periodStart ?? now()->subMonth()->toDateString();
        $end = $this->periodEnd ?? now()->toDateString();

        match ($this->reportType) {
            'sales' => $analyticsService->generateSalesReport($start, $end),
            'customers' => $analyticsService->generateCustomerReport($start, $end),
            'inventory' => $analyticsService->generateInventoryReport(),
            default => Log::warning("Unknown report type: {$this->reportType}"),
        };

        Log::info("Generated {$this->reportType} report for {$start} to {$end}");
    }
}
