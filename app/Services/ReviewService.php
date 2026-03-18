<?php

namespace App\Services;

use App\Repositories\ReviewRepository;
use App\Models\ProductReview;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReviewService
{
    public function __construct(
        protected ReviewRepository $reviewRepository
    ) {}

    public function getProductReviews(int $productId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->reviewRepository->getByProduct($productId, $perPage);
    }

    public function getProductRating(int $productId): array
    {
        return $this->reviewRepository->getProductRating($productId);
    }

    public function createReview(int $customerId, int $productId, array $data): ProductReview
    {
        $existing = $this->reviewRepository->findByCustomerAndProduct($customerId, $productId);
        if ($existing) {
            throw new \Exception('You have already reviewed this product.');
        }

        // Check if customer has a delivered or returned order with this product
        $isVerified = Order::where('customer_id', $customerId)
            ->whereHas('items', fn($q) => $q->where('product_id', $productId))
            ->whereIn('status', ['delivered', 'returned'])
            ->exists();

        // Auto-approve reviews from verified purchases
        $isApproved = $isVerified;

        return $this->reviewRepository->create([
            'product_id' => $productId,
            'customer_id' => $customerId,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'is_verified_purchase' => $isVerified,
            'is_approved' => $isApproved,
        ]);
    }

    /**
     * Check if customer has purchased this product (delivered or returned)
     */
    public function hasPurchasedProduct(int $customerId, int $productId): bool
    {
        return Order::where('customer_id', $customerId)
            ->whereHas('items', fn($q) => $q->where('product_id', $productId))
            ->whereIn('status', ['delivered', 'returned'])
            ->exists();
    }

    /**
     * Check if customer has already reviewed this product
     */
    public function hasReviewedProduct(int $customerId, int $productId): bool
    {
        return $this->reviewRepository->findByCustomerAndProduct($customerId, $productId) !== null;
    }

    public function updateReview(ProductReview $review, array $data): bool
    {
        return $this->reviewRepository->update($review, [
            'rating' => $data['rating'] ?? $review->rating,
            'title' => $data['title'] ?? $review->title,
            'body' => $data['body'] ?? $review->body,
            'is_approved' => false, // Re-approve after edit
        ]);
    }

    public function deleteReview(ProductReview $review): bool
    {
        return $this->reviewRepository->delete($review);
    }

    public function approveReview(int $reviewId): bool
    {
        $review = $this->reviewRepository->findById($reviewId);
        if (!$review) {
            throw new \Exception('Review not found.');
        }
        return $this->reviewRepository->update($review, ['is_approved' => true]);
    }

    public function getPendingReviews(int $perPage = 15): LengthAwarePaginator
    {
        return $this->reviewRepository->getPendingReviews($perPage);
    }

    public function getCustomerReviews(int $customerId): Collection
    {
        return $this->reviewRepository->getByCustomer($customerId);
    }
}
