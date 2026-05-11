<?php

namespace App\Console\Commands;

use App\Services\CartCleanupService;
use Illuminate\Console\Command;

class CleanupCartDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:cleanup-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge duplicate cart items (same product, same options) into one with combined quantity';

    /**
     * Execute the console command.
     */
    public function handle(CartCleanupService $cleanupService)
    {
        $this->info('Starting cart cleanup...');

        $merged = $cleanupService->mergeDuplicateCartItems();

        if (empty($merged)) {
            $this->info('✓ No duplicate cart items found!');
            return Command::SUCCESS;
        }

        $this->info('✓ Cart cleanup completed!');
        $this->info("Merged " . count($merged) . " duplicate group(s):");
        
        foreach ($merged as $item) {
            $this->line("  • Product ID {$item['product_id']}: {$item['message']}");
            $this->line("    New quantity: {$item['new_quantity']}");
        }

        return Command::SUCCESS;
    }
}
