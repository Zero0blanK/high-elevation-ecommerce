<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\Mail;

class InventoryService
{
    public function adjustStock(int $productId, int $quantity, string $type, $reference = null, string $notes = null)
    {
        $product = Product::findOrFail($productId);
        $quantityBefore = $product->stock_quantity;
        
        $product->stock_quantity += $quantity;
        $product->save();
        
        $this->logInventoryChange(
            $productId,
            $type,
            $quantityBefore,
            $quantity,
            $notes,
            $reference
        );
        
        // Check for low stock alert
        if ($product->isLowStock()) {
            $this->sendLowStockAlert($product);
        }
        
        return $product;
    }

    public function logInventoryChange(int $productId, string $type, int $quantityBefore, int $quantityChanged, string $notes = null, $reference = null)
    {
        InventoryLog::create([
            'product_id' => $productId,
            'type' => $type,
            'quantity_before' => $quantityBefore,
            'quantity_changed' => $quantityChanged,
            'quantity_after' => $quantityBefore + $quantityChanged,
            'reference_id' => $reference?->id,
            'reference_type' => $reference ? get_class($reference) : null,
            'notes' => $notes,
            'created_by' => auth('admin')->id()
        ]);
    }

    public function getLowStockProducts()
    {
        return Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('is_active', true)
            ->with('category')
            ->get();
    }

    public function getInventoryReport($dateFrom = null, $dateTo = null)
    {
        $query = InventoryLog::with('product')
            ->orderBy('created_at', 'desc');

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query->paginate(50);
    }

    public function bulkUpdateStock(array $updates)
    {
        foreach ($updates as $update) {
            $this->adjustStock(
                $update['product_id'],
                $update['quantity'],
                $update['type'] ?? 'adjustment',
                null,
                $update['notes'] ?? 'Bulk update'
            );
        }
    }

    private function sendLowStockAlert(Product $product)
    {
        // Get admin emails
        $adminEmails = \App\Models\AdminUser::where('is_active', true)
            ->pluck('email')
            ->toArray();

        if (!empty($adminEmails)) {
            Mail::to($adminEmails)->send(new \App\Mail\LowStockAlert($product));
        }
    }
}