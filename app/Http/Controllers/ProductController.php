<?php

namespace App\Http\Controllers;

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

        return view('products.show', compact('product', 'relatedProducts'));
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