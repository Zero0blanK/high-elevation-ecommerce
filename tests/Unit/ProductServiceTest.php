<?php

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can find product by slug', function () {
    $product = Product::factory()->create();
    $productService = app(ProductService::class);

    $found = $productService->findProductBySlug($product->slug);

    expect($found)->not->toBeNull();
    expect($found->id)->toBe($product->id);
});

test('can get filtered products by category', function () {
    $category = Category::factory()->create();
    Product::factory()->count(3)->create(['category_id' => $category->id]);
    Product::factory()->count(2)->create();

    $productService = app(ProductService::class);
    $products = $productService->getProductsByCategory($category->id, []);

    expect($products)->toHaveCount(3);
});

test('can get featured products', function () {
    Product::factory()->featured()->count(4)->create();
    Product::factory()->count(3)->create(['is_featured' => false]);

    $productService = app(ProductService::class);
    $featured = $productService->getFeaturedProducts(10);

    expect($featured)->toHaveCount(4);
});

test('active categories are returned', function () {
    Category::factory()->count(3)->create(['is_active' => true]);
    Category::factory()->inactive()->count(2)->create();

    $productService = app(ProductService::class);
    $categories = $productService->getActiveCategories();

    expect($categories)->toHaveCount(3);
});
