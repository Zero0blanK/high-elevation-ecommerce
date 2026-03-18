<?php

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('products page loads successfully', function () {
    Category::factory()->create();
    Product::factory()->count(3)->create();

    $response = $this->get(route('products.index'));
    $response->assertStatus(200);
});

test('product detail page loads', function () {
    $product = Product::factory()->create();

    $response = $this->get(route('products.show', $product->slug));
    $response->assertStatus(200);
    $response->assertSee($product->name);
});

test('products can be filtered by category', function () {
    $category = Category::factory()->create();
    Product::factory()->count(2)->create(['category_id' => $category->id]);
    Product::factory()->count(3)->create();

    $response = $this->get(route('products.category', $category->slug));
    $response->assertStatus(200);
});

test('homepage shows featured products', function () {
    Product::factory()->featured()->count(4)->create();

    $response = $this->get(route('home'));
    $response->assertStatus(200);
});
