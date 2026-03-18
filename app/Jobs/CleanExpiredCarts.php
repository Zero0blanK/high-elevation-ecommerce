<?php

namespace App\Jobs;

use App\Models\ShoppingCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanExpiredCarts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $expiredDays = config('ecommerce.orders.delete_incomplete_after', 7);

        $deleted = ShoppingCart::whereNull('customer_id')
            ->where('updated_at', '<', now()->subDays($expiredDays))
            ->delete();

        Log::info("Cleaned {$deleted} expired guest cart items");
    }
}
