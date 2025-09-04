<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low_stock':
                    $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
                case 'in_stock':
                    $query->where('stock_quantity', '>', 0);
                    break;
            }
        }

        $products = $query->orderBy('stock_quantity', 'asc')->paginate(20);
        $lowStockProducts = $this->inventoryService->getLowStockProducts();

        return view('admin.inventory.index', compact('products', 'lowStockProducts'));
    }

    public function logs(Request $request)
    {
        $logs = $this->inventoryService->getInventoryReport(
            $request->date_from,
            $request->date_to
        );

        return view('admin.inventory.logs', compact('logs'));
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.product_id' => 'required|exists:products,id',
            'updates.*.quantity' => 'required|integer',
            'updates.*.type' => 'required|in:adjustment,restock',
            'updates.*.notes' => 'nullable|string'
        ]);

        try {
            $this->inventoryService->bulkUpdateStock($request->updates);
            return back()->with('success', 'Inventory updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating inventory: ' . $e->getMessage());
        }
    }
}