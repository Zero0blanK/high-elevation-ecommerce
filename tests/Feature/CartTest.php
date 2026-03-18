<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('cart page loads', function () {
    $response = $this->get(route('cart.index'));
    $response->assertStatus(200);
});

test('product can be added to cart', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $response = $this->post(route('cart.add'), [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('shopping_cart', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('cart item can be removed', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $cartItem = ShoppingCart::create([
        'session_id' => session()->getId(),
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $response = $this->delete(route('cart.remove', $cartItem->id));
    $response->assertRedirect();
});

test('cart can be cleared', function () {
    $response = $this->delete(route('cart.clear'));
    $response->assertRedirect();
});
