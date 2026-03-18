{{-- Product Reviews Section - Include in products/show.blade.php --}}
<div class="mt-12" x-data="{ showReviewForm: false }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Customer Reviews</h2>
        @auth('customer')
            @if(isset($canReview) && $canReview)
                <button @click="showReviewForm = !showReviewForm"
                        class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition text-sm">
                    Write a Review
                </button>
            @elseif(isset($hasReviewed) && $hasReviewed)
                <span class="text-sm text-gray-500 italic">You have already reviewed this product</span>
            @else
                <span class="text-sm text-gray-500 italic">Purchase this product to leave a review</span>
            @endif
        @endauth
    </div>

    {{-- Review Form --}}
    @auth('customer')
    @if(isset($canReview) && $canReview)
    <div x-show="showReviewForm" x-transition class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Write Your Review</h3>
        <form action="{{ route('reviews.store', $product->id) }}" method="POST">
            @csrf
            <div class="mb-4" x-data="{ rating: 0, hoverRating: 0 }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button"
                                @click="rating = {{ $i }}"
                                @mouseenter="hoverRating = {{ $i }}"
                                @mouseleave="hoverRating = 0"
                                class="text-2xl focus:outline-none transition"
                                :class="(hoverRating || rating) >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">
                            ★
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" x-model="rating">
            </div>

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title (optional)</label>
                <input type="text" name="title" id="title" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500">
            </div>

            <div class="mb-4">
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Your Review (optional)</label>
                <textarea name="body" id="body" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500"></textarea>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="bg-amber-600 text-white px-6 py-2 rounded-lg hover:bg-amber-700 transition">
                    Submit Review
                </button>
                <button type="button" @click="showReviewForm = false" class="text-gray-600 hover:text-gray-800 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    @endif
    @endauth

    {{-- Reviews List --}}
    @if(isset($reviews) && $reviews->count() > 0)
        <div class="space-y-6">
            @foreach($reviews as $review)
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-2">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                    @endfor
                                </div>
                                @if($review->is_verified_purchase)
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Verified Purchase</span>
                                @endif
                            </div>
                            @if($review->title)
                                <h4 class="font-semibold text-gray-900 mt-1">{{ $review->title }}</h4>
                            @endif
                        </div>
                        <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                    </div>

                    @if($review->body)
                        <p class="text-gray-600 mt-3">{{ $review->body }}</p>
                    @endif

                    <p class="text-sm text-gray-400 mt-2">By {{ $review->customer->first_name }} {{ substr($review->customer->last_name, 0, 1) }}.</p>
                </div>
            @endforeach
        </div>

        @if($reviews->hasPages())
            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-8 bg-white rounded-lg shadow-sm">
            <p class="text-gray-500">No reviews yet. Be the first to review this product!</p>
        </div>
    @endif
</div>
