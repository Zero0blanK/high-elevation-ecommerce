<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\ProductReview;

class ProductReviewPolicy
{
    public function update(Customer $customer, ProductReview $review): bool
    {
        return $customer->id === $review->customer_id;
    }

    public function delete(Customer $customer, ProductReview $review): bool
    {
        return $customer->id === $review->customer_id;
    }
}
