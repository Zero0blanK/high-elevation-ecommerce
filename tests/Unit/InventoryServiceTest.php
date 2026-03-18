<?php

use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can adjust stock positively', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $inventoryService = app(InventoryService::class);

    $inventoryService->adjustStock($product->id, 5, 'restock', null, 'Test restock');

    expect($product->fresh()->stock_quantity)->toBe(15);
    $this->assertDatabaseHas('inventory_logs', [
        'product_id' => $product->id,
        'type' => 'restock',
        'quantity_changed' => 5,
    ]);
});

test('can adjust stock negatively for sale', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $inventoryService = app(InventoryService::class);

    $inventoryService->adjustStock($product->id, -3, 'sale', null, 'Test sale');

    expect($product->fresh()->stock_quantity)->toBe(7);
});

test('low stock products are identified', function () {
    Product::factory()->create(['stock_quantity' => 5, 'low_stock_threshold' => 10]);
    Product::factory()->create(['stock_quantity' => 50, 'low_stock_threshold' => 10]);

    $inventoryService = app(InventoryService::class);
    $lowStock = $inventoryService->getLowStockProducts();

    expect($lowStock)->toHaveCount(1);
});
