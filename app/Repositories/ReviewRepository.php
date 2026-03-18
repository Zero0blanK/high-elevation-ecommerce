<?php

namespace App\Repositories;

use App\Models\ProductReview;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReviewRepository
{
    protected ProductReview $model;

    public function __construct(ProductReview $review)
    {
        $this->model = $review;
    }

    public function getByProduct(int $productId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('customer')
            ->where('product_id', $productId)
            ->where('is_approved', true)
            ->latest()
            ->paginate($perPage);
    }

    public function getAllByProduct(int $productId): Collection
    {
        return $this->model->newQuery()
            ->with('customer')
            ->where('product_id', $productId)
            ->latest()
            ->get();
    }

    public function findById(int $id): ?ProductReview
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByCustomerAndProduct(int $customerId, int $productId): ?ProductReview
    {
        return $this->model->newQuery()
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();
    }

    public function create(array $data): ProductReview
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(ProductReview $review, array $data): bool
    {
        return $review->update($data);
    }

    public function delete(ProductReview $review): bool
    {
        return $review->delete();
    }

    public function getProductRating(int $productId): array
    {
        $reviews = $this->model->newQuery()
            ->where('product_id', $productId)
            ->where('is_approved', true);

        return [
            'average' => round($reviews->avg('rating'), 1),
            'count' => $reviews->count(),
        ];
    }

    public function getPendingReviews(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['customer', 'product'])
            ->where('is_approved', false)
            ->latest()
            ->paginate($perPage);
    }

    public function getByCustomer(int $customerId): Collection
    {
        return $this->model->newQuery()
            ->with('product')
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
    }
}
