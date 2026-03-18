<?php

namespace App\Repositories;

use App\Models\Wishlist;
use Illuminate\Support\Collection;

class WishlistRepository
{
    protected Wishlist $model;

    public function __construct(Wishlist $wishlist)
    {
        $this->model = $wishlist;
    }

    public function getByCustomer(int $customerId): Collection
    {
        return $this->model->newQuery()
            ->with(['product.primaryImage', 'product.category'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
    }

    public function findByCustomerAndProduct(int $customerId, int $productId): ?Wishlist
    {
        return $this->model->newQuery()
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();
    }

    public function create(array $data): Wishlist
    {
        return $this->model->newQuery()->create($data);
    }

    public function delete(Wishlist $wishlist): bool
    {
        return $wishlist->delete();
    }

    public function removeByCustomerAndProduct(int $customerId, int $productId): bool
    {
        return $this->model->newQuery()
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    public function isInWishlist(int $customerId, int $productId): bool
    {
        return $this->model->newQuery()
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->exists();
    }

    public function getCount(int $customerId): int
    {
        return $this->model->newQuery()
            ->where('customer_id', $customerId)
            ->count();
    }
}
