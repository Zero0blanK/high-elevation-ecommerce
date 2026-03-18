<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function createProduct(array $data): Product
    {
        $data['slug'] = $this->generateUniqueSlug($data['name']);
        $data['stock_quantity'] = $data['stock_quantity'] ?? 0;
        
        $product = $this->productRepository->create($data);
        
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
        
        $this->productRepository->update($product, $data);
        
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

    public function deleteProduct(Product $product): bool
    {
        return $this->productRepository->delete($product);
    }

    public function findProductById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function findProductBySlug(string $slug): ?Product
    {
        return $this->productRepository->findBySlug($slug);
    }

    public function findProductWithRelations(int $id, array $relations = []): ?Product
    {
        return $this->productRepository->findWithRelations($id, $relations);
    }

    public function addProductImage(Product $product, UploadedFile $file, bool $isPrimary = false): ProductImage
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images/products'), $filename);
        $imageUrl = '/images/products/' . $filename;
        
        // If this is set as primary, remove primary flag from other images
        if ($isPrimary) {
            $this->productRepository->clearPrimaryImages($product->id);
        }
        
        $sortOrder = $this->productRepository->getMaxImageSortOrder($product->id) + 1;
        
        return $this->productRepository->createImage($product->id, [
            'image_url' => $imageUrl,
            'alt_text' => $product->name,
            'is_primary' => $isPrimary,
            'sort_order' => $sortOrder
        ]);
    }

    public function getProductsByCategory($categoryId = null, $filters = []): LengthAwarePaginator
    {
        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }

        return $this->productRepository->getFilteredProducts($filters);
    }

    public function searchProducts(string $searchTerm, array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->searchProducts($searchTerm, $filters);
    }

    public function getFilteredProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->productRepository->getFilteredProducts($filters, $perPage);
    }

    public function getRelatedProducts(Product $product, int $limit = 4): Collection
    {
        return $this->productRepository->getRelatedProducts($product, $limit);
    }

    public function getFeaturedProducts(int $limit = 4): Collection
    {
        return $this->productRepository->getFeaturedProducts($limit);
    }

    public function getActiveCategories(): Collection
    {
        return $this->productRepository->getActiveCategories();
    }

    public function getCategoriesWithProductCount(): Collection
    {
        return $this->productRepository->getCategoriesWithProductCount();
    }

    public function findCategoryBySlug(string $slug): ?Category
    {
        return $this->productRepository->findCategoryBySlug($slug);
    }

    public function updateStock(Product $product, int $quantity): bool
    {
        return $this->productRepository->updateStock($product, $quantity);
    }

    public function decrementStock(Product $product, int $quantity): bool
    {
        return $this->productRepository->decrementStock($product, $quantity);
    }

    public function incrementStock(Product $product, int $quantity): bool
    {
        return $this->productRepository->incrementStock($product, $quantity);
    }

    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->productRepository->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
}