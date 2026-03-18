<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    public function store(ReviewRequest $request, $productId)
    {
        $customer = Auth::guard('customer')->user();

        try {
            $this->reviewService->createReview($customer->id, $productId, $request->validated());
            return back()->with('success', 'Thank you! Your review has been submitted for approval.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(ReviewRequest $request, $reviewId)
    {
        $customer = Auth::guard('customer')->user();
        $review = $this->reviewService->getCustomerReviews($customer->id)
            ->firstWhere('id', $reviewId);

        if (!$review || $review->customer_id !== $customer->id) {
            abort(403);
        }

        $this->reviewService->updateReview($review, $request->validated());
        return back()->with('success', 'Review updated successfully.');
    }

    public function destroy($reviewId)
    {
        $customer = Auth::guard('customer')->user();
        $review = $this->reviewService->getCustomerReviews($customer->id)
            ->firstWhere('id', $reviewId);

        if (!$review || $review->customer_id !== $customer->id) {
            abort(403);
        }

        $this->reviewService->deleteReview($review);
        return back()->with('success', 'Review deleted.');
    }
}
