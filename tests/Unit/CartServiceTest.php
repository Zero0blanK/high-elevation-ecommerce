<?php

use App\Models\Product;
use App\Models\Customer;
use App\Models\ShoppingCart;
use App\Services\CartService;
use App\Repositories\CartRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can add product to cart', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $cartService = app(CartService::class);

    $result = $cartService->addToCart($product->id, 2, [], null, 'test-session');

    expect($result)->not->toBeNull();
    $this->assertDatabaseHas('shopping_cart', [
        'product_id' => $product->id,
        'quantity' => 2,
        'session_id' => 'test-session',
    ]);
});

test('cannot add out of stock product', function () {
    $product = Product::factory()->outOfStock()->create();
    $cartService = app(CartService::class);

    $this->expectException(\Exception::class);
    $cartService->addToCart($product->id, 1, [], null, 'test-session');
});

test('cart totals are calculated correctly', function () {
    $product1 = Product::factory()->create(['price' => 10.00, 'stock_quantity' => 50]);
    $product2 = Product::factory()->create(['price' => 20.00, 'stock_quantity' => 50]);

    $cartService = app(CartService::class);
    $cartService->addToCart($product1->id, 2, [], null, 'test-session');
    $cartService->addToCart($product2->id, 1, [], null, 'test-session');

    $totals = $cartService->getCartTotals(null, 'test-session');

    expect($totals['subtotal'])->toBe(40.0);
    expect($totals['item_count'])->toBe(3);
});

test('can increase cart item quantity', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $cartService = app(CartService::class);

    $item = $cartService->addToCart($product->id, 1, [], null, 'test-session');
    $cartService->increaseQuantity($item->id, null, 'test-session');

    $this->assertDatabaseHas('shopping_cart', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});
