<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Console\Command;

class SyncInventoryLevels extends Command
{
    protected $signature = 'inventory:sync {--file= : CSV file path to sync inventory from}';
    protected $description = 'Sync inventory levels from external source';

    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        parent::__construct();
        $this->inventoryService = $inventoryService;
    }

    public function handle()
    {
        $filePath = $this->option('file');

        if (!$filePath) {
            $this->error('Please provide a CSV file path using --file option');
            return 1;
        }

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info('Syncing inventory levels from CSV file...');

        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle);
        
        $updatedCount = 0;
        $errorCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            
            try {
                $product = Product::where('sku', $data['sku'])->first();
                
                if ($product) {
                    $newQuantity = (int) $data['quantity'];
                    $quantityDiff = $newQuantity - $product->stock_quantity;
                    
                    if ($quantityDiff != 0) {
                        $this->inventoryService->adjustStock(
                            $product->id,
                            $quantityDiff,
                            'sync',
                            null,
                            'Inventory sync from ' . basename($filePath)
                        );
                        
                        $this->line("Updated {$product->sku}: {$product->stock_quantity} -> {$newQuantity}");
                        $updatedCount++;
                    }
                } else {
                    $this->warn("Product not found: {$data['sku']}");
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $this->error("Error processing {$data['sku']}: " . $e->getMessage());
                $errorCount++;
            }
        }

        fclose($handle);

        $this->info("Inventory sync completed. Updated: {$updatedCount}, Errors: {$errorCount}");
        return 0;
    }
}