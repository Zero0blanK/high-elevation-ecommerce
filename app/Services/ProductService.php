<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function createProduct(array $data): Product
    {
        $data['slug'] = $this->generateUniqueSlug($data['name']);
        
        $product = Product::create($data);
        
        // Log initial inventory
        if ($product->stock_quantity > 0) {
            app(InventoryService::class)->logInventoryChange(
                $product->id,
                'restock',
                0,
                $product->stock_quantity,
                'Initial stock'
            );
        }
        
        return $product;
    }

    public function updateProduct(Product $product, array $data): Product
    {
        $oldStock = $product->stock_quantity;
        
        if (isset($data['name']) && $data['name'] !== $product->name) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $product->id);
        }
        
        $product->update($data);
        
        // Log inventory change if stock quantity changed
        if (isset($data['stock_quantity']) && $data['stock_quantity'] != $oldStock) {
            app(InventoryService::class)->logInventoryChange(
                $product->id,
                'adjustment',
                $oldStock,
                $data['stock_quantity'] - $oldStock,
                'Manual adjustment'
            );
        }
        
        return $product->fresh();
    }

    public function addProductImage(Product $product, UploadedFile $file, bool $isPrimary = false): ProductImage
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('products', $filename, 'public');
        
        // If this is set as primary, remove primary flag from other images
        if ($isPrimary) {
            $product->images()->update(['is_primary' => false]);
        }
        
        return $product->images()->create([
            'image_url' => Storage::url($path),
            'alt_text' => $product->name,
            'is_primary' => $isPrimary,
            'sort_order' => $product->images()->max('sort_order') + 1
        ]);
    }

    public function getProductsByCategory($categoryId = null, $filters = [])
    {
        $query = Product::with(['category', 'primaryImage'])
            ->active()
            ->inStock();

        if ($categoryId) {
            $query->byCategory($categoryId);
        }

        if (isset($filters['roast_level'])) {
            $query->where('roast_level', $filters['roast_level']);
        }

        if (isset($filters['grind_type'])) {
            $query->where('grind_type', $filters['grind_type']);
        }

        if (isset($filters['origin'])) {
            $query->where('origin', 'like', '%' . $filters['origin'] . '%');
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['featured'])) {
            $query->featured();
        }

        $sortBy = $filters['sort'] ?? 'name';
        $sortDirection = $filters['direction'] ?? 'asc';

        return $query->orderBy($sortBy, $sortDirection)->paginate(12);
    }

    public function searchProducts($searchTerm, $filters = [])
    {
        $query = Product::with(['category', 'primaryImage'])
            ->active()
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('origin', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });

        return $this->applyFilters($query, $filters);
    }

    private function applyFilters($query, $filters = [])
    {
        if (isset($filters['roast_level'])) {
            $query->where('roast_level', $filters['roast_level']);
        }

        if (isset($filters['grind_type'])) {
            $query->where('grind_type', $filters['grind_type']);
        }

        if (isset($filters['origin'])) {
            $query->where('origin', 'like', '%' . $filters['origin'] . '%');
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['featured'])) {
            $query->featured();
        }

        $sortBy = $filters['sort'] ?? 'name';
        $sortDirection = $filters['direction'] ?? 'asc';

        return $query->orderBy($sortBy, $sortDirection)->paginate(12);
    }

    private function generateUniqueSlug($name, $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Product::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if (!$query->exists()) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
    
    public function getFilteredProducts($filters = [], $perPage = 12)
    {
        $query = Product::with(['category', 'primaryImage'])
            ->active()
            ->inStock();

        // Apply filters
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['roast_level'])) {
            $query->where('roast_level', $filters['roast_level']);
        }

        if (!empty($filters['featured'])) {
            $query->where('is_featured', true);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                ->orWhere('short_description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply sorting
        $sortField = $filters['sort'] ?? 'name';
        $sortDirection = $filters['direction'] ?? 'asc';
        
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    public function getRelatedProducts($product, $limit = 4)
    {
        return Product::with(['category', 'primaryImage'])
            ->active()
            ->inStock()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->limit($limit)
            ->get();
    }
}