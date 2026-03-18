<?php

namespace App\Repositories;

use App\Models\ShoppingCart;
use Illuminate\Support\Collection;

class CartRepository
{
    protected ShoppingCart $model;

    public function __construct(ShoppingCart $shoppingCart)
    {
        $this->model = $shoppingCart;
    }

    /**
     * Get cart items with product relationships
     */
    public function getCartWithProducts(?int $customerId, ?string $sessionId): Collection
    {
        $query = $this->model->newQuery()
            ->with(['product.primaryImage', 'product.category']);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->get();
    }

    /**
     * Find a specific cart item by ID with ownership check
     */
    public function findCartItem(int $cartItemId, ?int $customerId, ?string $sessionId): ?ShoppingCart
    {
        $query = $this->model->newQuery()->where('id', $cartItemId);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->first();
    }

    /**
     * Find existing cart item by product, owner, and options
     */
    public function findExistingCartItem(
        int $productId,
        ?int $customerId,
        ?string $sessionId,
        array $options = []
    ): ?ShoppingCart {
        $query = $this->model->newQuery()
            ->where('product_id', $productId)
            ->where('product_options', json_encode($options));

        if ($customerId) {
            $query->where('customer_id', $customerId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->first();
    }

    /**
     * Create a new cart item
     */
    public function create(array $data): ShoppingCart
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * Update a cart item
     */
    public function update(ShoppingCart $cartItem, array $data): bool
    {
        return $cartItem->update($data);
    }

    /**
     * Delete a cart item
     */
    public function delete(ShoppingCart $cartItem): bool
    {
        return $cartItem->delete();
    }

    /**
     * Delete cart items by customer or session
     */
    public function clearCart(?int $customerId, ?string $sessionId): int
    {
        $query = $this->model->newQuery();

        if ($customerId) {
            $query->where('customer_id', $customerId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->delete();
    }

    /**
     * Get guest cart items by session ID
     */
    public function getGuestCartItems(string $sessionId): Collection
    {
        return $this->model->newQuery()
            ->forGuest($sessionId)
            ->with('product')
            ->get();
    }

    /**
     * Get customer cart items
     */
    public function getCustomerCartItems(int $customerId): Collection
    {
        return $this->model->newQuery()
            ->forCustomer($customerId)
            ->with('product')
            ->get();
    }

    /**
     * Find customer's cart item by product and options
     */
    public function findCustomerCartItem(
        int $customerId,
        int $productId,
        mixed $productOptions
    ): ?ShoppingCart {
        return $this->model->newQuery()
            ->forCustomer($customerId)
            ->where('product_id', $productId)
            ->where('product_options', $productOptions)
            ->first();
    }

    /**
     * Increment cart item quantity
     */
    public function incrementQuantity(ShoppingCart $cartItem): void
    {
        $cartItem->increment('quantity');
    }

    /**
     * Decrement cart item quantity
     */
    public function decrementQuantity(ShoppingCart $cartItem): void
    {
        $cartItem->decrement('quantity');
    }
}