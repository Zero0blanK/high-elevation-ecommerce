<?php

namespace App\Observers;

use App\Models\Product;
use App\Events\LowStockAlert;
use Illuminate\Support\Str;

class ProductObserver
{
    public function creating(Product $product): void
    {
        if (empty($product->slug)) {
            $product->slug = $this->generateUniqueSlug($product->name);
        }

        if (empty($product->sku)) {
            $product->sku = 'HE-' . strtoupper(Str::random(8));
        }
    }

    public function updated(Product $product): void
    {
        if ($product->isDirty('stock_quantity')) {
            $newStock = $product->stock_quantity;
            if ($newStock <= $product->low_stock_threshold && $newStock > 0) {
                event(new LowStockAlert($product, $newStock, $product->low_stock_threshold));
            }
        }
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }
}
