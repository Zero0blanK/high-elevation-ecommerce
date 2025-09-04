<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Console\Command;

class SendLowStockAlerts extends Command
{
    protected $signature = 'inventory:low-stock-alerts';
    protected $description = 'Check inventory and send low stock alerts';

    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        parent::__construct();
        $this->inventoryService = $inventoryService;
    }

    public function handle()
    {
        $this->info('Checking for low stock products...');

        $lowStockProducts = $this->inventoryService->getLowStockProducts();

        if ($lowStockProducts->isEmpty()) {
            $this->info('No low stock products found.');
            return 0;
        }

        $this->warn("Found {$lowStockProducts->count()} low stock products:");

        foreach ($lowStockProducts as $product) {
            $this->line("- {$product->name} (SKU: {$product->sku}) - Stock: {$product->stock_quantity}");
        }

        // Send consolidated low stock alert
        if (config('ecommerce.emails.low_stock.enabled')) {
            \Mail::to(config('ecommerce.emails.low_stock.recipients'))
                ->send(new \App\Mail\LowStockReport($lowStockProducts));
            
            $this->info('Low stock alert email sent to administrators.');
        }

        return 0;
    }
}