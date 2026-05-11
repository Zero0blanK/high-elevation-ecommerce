<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCombinationTest extends TestCase
{
    use RefreshDatabase;

    private CartService $cartService;
    private Customer $customer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->cartService = app(CartService::class);
        
        // Create test customer and product
        $this->customer = Customer::factory()->create();
        $this->product = Product::factory()->create([
            'stock_quantity' => 100,
            'is_in_stock' => true
        ]);
    }

    public function test_adding_same_product_multiple_times_combines_quantity()
    {
        // Add product 1st time with quantity 2
        $item1 = $this->cartService->addToCart(
            $this->product->id,
            2,
            [],
            $this->customer->id
        );
        
        $this->assertEquals(2, $item1->quantity);
        
        // Add same product 2nd time with quantity 3
        $item2 = $this->cartService->addToCart(
            $this->product->id,
            3,
            [],
            $this->customer->id
        );
        
        // Should return the same cart item with combined quantity
        $this->assertEquals($item1->id, $item2->id);
        $this->assertEquals(5, $item2->quantity);
        
        // Verify only one cart item exists for this product
        $cartItems = ShoppingCart::where('customer_id', $this->customer->id)
            ->where('product_id', $this->product->id)
            ->get();
        
        $this->assertCount(1, $cartItems);
        $this->assertEquals(5, $cartItems->first()->quantity);
    }

    public function test_adding_same_product_without_options_combines()
    {
        // Add product with explicit empty options
        $item1 = $this->cartService->addToCart(
            $this->product->id,
            1,
            [],
            $this->customer->id
        );
        
        // Add same product again with empty options (no product_options parameter)
        $item2 = $this->cartService->addToCart(
            $this->product->id,
            1,
            [],
            $this->customer->id
        );
        
        $this->assertEquals($item1->id, $item2->id);
        $this->assertEquals(2, $item2->quantity);
    }

    public function test_adding_different_options_creates_separate_items()
    {
        $options1 = ['grind' => 'coarse'];
        $options2 = ['grind' => 'fine'];
        
        // Add product with first option set
        $item1 = $this->cartService->addToCart(
            $this->product->id,
            1,
            $options1,
            $this->customer->id
        );
        
        // Add same product with different option set
        $item2 = $this->cartService->addToCart(
            $this->product->id,
            1,
            $options2,
            $this->customer->id
        );
        
        // Should create separate items
        $this->assertNotEquals($item1->id, $item2->id);
        
        // Verify two cart items exist
        $cartItems = ShoppingCart::where('customer_id', $this->customer->id)
            ->where('product_id', $this->product->id)
            ->get();
        
        $this->assertCount(2, $cartItems);
    }

    public function test_get_cart_totals_with_combined_items()
    {
        // Add same product 3 times
        $this->cartService->addToCart($this->product->id, 1, [], $this->customer->id);
        $this->cartService->addToCart($this->product->id, 2, [], $this->customer->id);
        $this->cartService->addToCart($this->product->id, 3, [], $this->customer->id);
        
        $totals = $this->cartService->getCartTotals($this->customer->id);
        
        // Should have 1 cart item with quantity 6
        $this->assertCount(1, $totals['items']);
        $this->assertEquals(6, $totals['total_items']);
    }
}
