<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $filters = [
            'roast_level' => $request->roast_level,
            'grind_type' => $request->grind_type,
            'origin' => $request->origin,
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'featured' => $request->featured,
            'sort' => $request->sort ?? 'name',
            'direction' => $request->direction ?? 'asc'
        ];

        $products = $this->productService->getProductsByCategory(
            $request->category_id,
            $filters
        );

        return new ProductCollection($products);
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->load(['category', 'images']);
        
        // Get related products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->limit(4)
            ->get();

        return response()->json([
            'data' => new ProductResource($product),
            'related_products' => ProductResource::collection($relatedProducts)
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $filters = [
            'roast_level' => $request->roast_level,
            'grind_type' => $request->grind_type,
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'sort' => $request->sort ?? 'relevance',
            'direction' => $request->direction ?? 'desc'
        ];

        $products = $this->productService->searchProducts($request->q, $filters);

        return new ProductCollection($products);
    }

    public function categories()
    {
        $categories = Category::active()
            ->withCount(['activeProducts'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $categories
        ]);
    }

    public function featured()
    {
        $products = Product::with(['category', 'primaryImage'])
            ->active()
            ->featured()
            ->inStock()
            ->limit(8)
            ->get();

        return response()->json([
            'data' => ProductResource::collection($products)
        ]);
    }
}