<?php

namespace App\Services;

use App\Repositories\WishlistRepository;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Collection;

class WishlistService
{
    public function __construct(
        protected WishlistRepository $wishlistRepository
    ) {}

    public function getWishlist(int $customerId): Collection
    {
        return $this->wishlistRepository->getByCustomer($customerId);
    }

    public function toggle(int $customerId, int $productId): array
    {
        $product = Product::findOrFail($productId);

        $existing = $this->wishlistRepository->findByCustomerAndProduct($customerId, $productId);

        if ($existing) {
            $this->wishlistRepository->delete($existing);
            return ['added' => false, 'message' => "{$product->name} removed from wishlist"];
        }

        $this->wishlistRepository->create([
            'customer_id' => $customerId,
            'product_id' => $productId,
        ]);

        return ['added' => true, 'message' => "{$product->name} added to wishlist"];
    }

    public function add(int $customerId, int $productId): Wishlist
    {
        Product::findOrFail($productId);

        $existing = $this->wishlistRepository->findByCustomerAndProduct($customerId, $productId);
        if ($existing) {
            return $existing;
        }

        return $this->wishlistRepository->create([
            'customer_id' => $customerId,
            'product_id' => $productId,
        ]);
    }

    public function remove(int $customerId, int $productId): bool
    {
        return $this->wishlistRepository->removeByCustomerAndProduct($customerId, $productId);
    }

    public function isInWishlist(int $customerId, int $productId): bool
    {
        return $this->wishlistRepository->isInWishlist($customerId, $productId);
    }

    public function getCount(int $customerId): int
    {
        return $this->wishlistRepository->getCount($customerId);
    }
}
