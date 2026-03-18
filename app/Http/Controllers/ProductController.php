<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;
    protected $reviewService;

    public function __construct(ProductService $productService, ReviewService $reviewService)
    {
        $this->productService = $productService;
        $this->reviewService = $reviewService;
    }

    public function index(Request $request)
    {
        $filters = [
            'category_id' => $request->get('category'),
            'roast_level' => $request->get('roast_level'),
            'featured' => $request->get('featured'),
            'search' => $request->get('search'),
            'sort' => $request->get('sort', 'name'),
            'direction' => $request->get('direction', 'asc'),
        ];

        $products = $this->productService->getFilteredProducts($filters, 12);
        $categories = Category::active()->orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'filters'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['category', 'primaryImage']);
        $relatedProducts = $this->productService->getRelatedProducts($product, 4);
        $reviews = $this->reviewService->getProductReviews($product->id, 10);

        // Check if logged-in customer can write a review
        $canReview = false;
        $hasReviewed = false;
        $customer = auth('customer')->user();
        
        if ($customer) {
            $hasPurchased = $this->reviewService->hasPurchasedProduct($customer->id, $product->id);
            $hasReviewed = $this->reviewService->hasReviewedProduct($customer->id, $product->id);
            $canReview = $hasPurchased && !$hasReviewed;
        }

        return view('products.show', compact('product', 'relatedProducts', 'reviews', 'canReview', 'hasReviewed'));
    }

    public function category($categorySlug)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        
        $filters = [
            'category_id' => $category->id,
            'sort' => request('sort', 'name'),
            'direction' => request('direction', 'asc'),
        ];

        $products = $this->productService->getFilteredProducts($filters, 12);
        $categories = Category::active()->orderBy('name')->get();

        return view('products.category', compact('products', 'categories', 'category', 'filters'));
    }
}