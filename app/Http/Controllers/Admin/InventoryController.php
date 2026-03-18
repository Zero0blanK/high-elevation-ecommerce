<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use App\Models\Product;
use App\Models\InventoryLog;
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

        $totalProducts = Product::count();
        $totalStock = Product::sum('stock_quantity');
        $lowStockCount = $lowStockProducts->count();
        $outOfStockCount = Product::where('stock_quantity', 0)->count();

        return view('admin.inventory.index', compact(
            'products', 'lowStockProducts', 'totalProducts', 'totalStock', 'lowStockCount', 'outOfStockCount'
        ));
    }

    public function stockInForm()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.inventory.stock-in', compact('products'));
    }

    public function stockIn(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ]);

        try {
            $refNote = $request->reference_number ? "Ref: {$request->reference_number}" : null;
            foreach ($request->items as $item) {
                $notes = trim(($item['notes'] ?? '') . ($refNote ? " | $refNote" : ''));
                $this->inventoryService->adjustStock(
                    $item['product_id'],
                    abs($item['quantity']),
                    'restock',
                    null,
                    $notes ?: 'Stock In'
                );
            }
            return redirect()->route('admin.inventory.index')->with('success', 'Stock in recorded successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error recording stock in: ' . $e->getMessage());
        }
    }

    public function stockOutForm()
    {
        $products = Product::where('is_active', true)->where('stock_quantity', '>', 0)->orderBy('name')->get();
        return view('admin.inventory.stock-out', compact('products'));
    }

    public function stockOut(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.reason' => 'required|in:damaged,expired,returned,manual_adjustment,other',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        try {
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($item['quantity'] > $product->stock_quantity) {
                    return back()->withInput()->with('error', "Cannot remove {$item['quantity']} units from {$product->name}. Only {$product->stock_quantity} available.");
                }
                $notes = ucfirst(str_replace('_', ' ', $item['reason']));
                if (!empty($item['notes'])) {
                    $notes .= ": {$item['notes']}";
                }
                $this->inventoryService->adjustStock(
                    $item['product_id'],
                    -abs($item['quantity']),
                    'adjustment',
                    null,
                    $notes
                );
            }
            return redirect()->route('admin.inventory.index')->with('success', 'Stock out recorded successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error recording stock out: ' . $e->getMessage());
        }
    }

    public function logs(Request $request)
    {
        $query = InventoryLog::with(['product']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);
        $products = Product::orderBy('name')->get();

        return view('admin.inventory.logs', compact('logs', 'products'));
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