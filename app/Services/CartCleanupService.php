<?php

namespace App\Services;

use App\Models\ShoppingCart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartCleanupService
{
    /**
     * Merge duplicate cart items (same product, same options) into one
     * Useful for cleaning up cart after fixing duplicate logic
     */
    public function mergeDuplicateCartItems(): array
    {
        $merged = [];

        // Get all cart items grouped by (customer_id/session_id, product_id, product_options)
        $duplicates = ShoppingCart::select(
            'customer_id',
            'session_id',
            'product_id',
            'product_options',
            DB::raw('GROUP_CONCAT(id) as ids'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(quantity) as total_quantity')
        )
        ->groupBy('customer_id', 'session_id', 'product_id', 'product_options')
        ->having(DB::raw('COUNT(*)'), '>', 1)
        ->get();

        foreach ($duplicates as $dup) {
            $ids = explode(',', $dup->ids);
            
            if (count($ids) > 1) {
                // Keep the first item and update its quantity
                $keepId = $ids[0];
                $deleteIds = array_slice($ids, 1);

                ShoppingCart::where('id', $keepId)
                    ->update(['quantity' => $dup->total_quantity]);

                ShoppingCart::whereIn('id', $deleteIds)->delete();

                $merged[] = [
                    'product_id' => $dup->product_id,
                    'customer_id' => $dup->customer_id,
                    'session_id' => $dup->session_id,
                    'kept_id' => $keepId,
                    'merged_ids' => $deleteIds,
                    'new_quantity' => $dup->total_quantity,
                    'message' => "Merged " . count($deleteIds) . " duplicate entries"
                ];

                Log::info("Cart cleanup: merged {$dup->product_id} for customer {$dup->customer_id}/" . ($dup->session_id ?? 'session'), [
                    'kept_id' => $keepId,
                    'deleted_ids' => $deleteIds,
                    'new_quantity' => $dup->total_quantity
                ]);
            }
        }

        return $merged;
    }
}
