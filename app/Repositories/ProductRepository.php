<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    protected Product $product;
    protected Category $category;
    protected ProductImage $productImage;

    public function __construct(
        Product $product,
        Category $category,
        ProductImage $productImage
    ) {
        $this->product = $product;
        $this->category = $category;
        $this->productImage = $productImage;
    }

    /*
    |--------------------------------------------------------------------------
    | Product Methods
    |--------------------------------------------------------------------------
    */

    public function findById(int $id): ?Product
    {
        return $this->product->newQuery()->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->product->newQuery()->where('slug', $slug)->first();
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->product->newQuery()->where('sku', $sku)->first();
    }

    public function findWithRelations(int $id, array $relations = []): ?Product
    {
        return $this->product->newQuery()->with($relations)->find($id);
    }

    public function create(array $data): Product
    {
        return $this->product->newQuery()->create($data);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function getActiveProducts(int $limit = null): Collection
    {
        $query = $this->product->newQuery()
            ->with(['category', 'primaryImage'])
            ->active()
            ->inStock();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getFeaturedProducts(int $limit = 4): Collection
    {
        return $this->product->newQuery()
            ->with(['category', 'primaryImage'])
            ->active()
            ->featured()
            ->limit($limit)
            ->get();
    }

    public function getFilteredProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->product->newQuery()
            ->with(['category', 'primaryImage'])
            ->active()
            ->inStock();

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['roast_level'])) {
            $query->where('roast_level', $filters['roast_level']);
        }

        if (!empty($filters['grind_type'])) {
            $query->where('grind_type', $filters['grind_type']);
        }

        if (!empty($filters['origin'])) {
            $query->where('origin', 'like', '%' . $filters['origin'] . '%');
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['featured'])) {
            $query->featured();
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('short_description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('sku', 'like', '%' . $filters['search'] . '%');
            });
        }

        $sortField = $filters['sort'] ?? 'name';
        $sortDirection = $filters['direction'] ?? 'asc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function getRelatedProducts(Product $product, int $limit = 4): Collection
    {
        return $this->product->newQuery()
            ->with(['category', 'primaryImage'])
            ->active()
            ->inStock()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->limit($limit)
            ->get();
    }

    public function searchProducts(string $searchTerm, array $filters = []): LengthAwarePaginator
    {
        $query = $this->product->newQuery()
            ->with(['category', 'primaryImage'])
            ->active()
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('origin', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });

        return $this->applyFiltersAndPaginate($query, $filters);
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = $this->product->newQuery()->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function updateStock(Product $product, int $quantity): bool
    {
        return $product->update(['stock_quantity' => $quantity]);
    }

    public function decrementStock(Product $product, int $quantity): bool
    {
        return $product->decrement('stock_quantity', $quantity) > 0;
    }

    public function incrementStock(Product $product, int $quantity): bool
    {
        return $product->increment('stock_quantity', $quantity) > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Category Methods
    |--------------------------------------------------------------------------
    */

    public function getAllCategories(): Collection
    {
        return $this->category->newQuery()->orderBy('name')->get();
    }

    public function getActiveCategories(): Collection
    {
        return $this->category->newQuery()->active()->orderBy('name')->get();
    }

    public function findCategoryById(int $id): ?Category
    {
        return $this->category->newQuery()->find($id);
    }

    public function findCategoryBySlug(string $slug): ?Category
    {
        return $this->category->newQuery()->where('slug', $slug)->first();
    }

    public function createCategory(array $data): Category
    {
        return $this->category->newQuery()->create($data);
    }

    public function updateCategory(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function deleteCategory(Category $category): bool
    {
        return $category->delete();
    }

    public function getCategoriesWithProductCount(): Collection
    {
        return $this->category->newQuery()
            ->active()
            ->withCount(['products as active_products_count' => function ($query) {
                $query->active();
            }])
            ->orderBy('name')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Product Image Methods
    |--------------------------------------------------------------------------
    */

    public function createImage(int $productId, array $data): ProductImage
    {
        return $this->productImage->newQuery()->create(
            array_merge($data, ['product_id' => $productId])
        );
    }

    public function updateImage(ProductImage $image, array $data): bool
    {
        return $image->update($data);
    }

    public function deleteImage(ProductImage $image): bool
    {
        return $image->delete();
    }

    public function clearPrimaryImages(int $productId): int
    {
        return $this->productImage->newQuery()
            ->where('product_id', $productId)
            ->update(['is_primary' => false]);
    }

    public function getMaxImageSortOrder(int $productId): int
    {
        return $this->productImage->newQuery()
            ->where('product_id', $productId)
            ->max('sort_order') ?? 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Private Methods
    |--------------------------------------------------------------------------
    */

    private function applyFiltersAndPaginate($query, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        if (!empty($filters['roast_level'])) {
            $query->where('roast_level', $filters['roast_level']);
        }

        if (!empty($filters['grind_type'])) {
            $query->where('grind_type', $filters['grind_type']);
        }

        if (!empty($filters['origin'])) {
            $query->where('origin', 'like', '%' . $filters['origin'] . '%');
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['featured'])) {
            $query->featured();
        }

        $sortField = $filters['sort'] ?? 'name';
        $sortDirection = $filters['direction'] ?? 'asc';

        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }
}
