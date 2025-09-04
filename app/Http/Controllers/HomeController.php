<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {

        $featuredProducts = Product::with(['category', 'primaryImage'])
            ->active()
            ->featured()
            ->limit(4)
            ->get();

        $categories = Category::active()
            ->withCount(['products as active_products_count' => function ($query) {
                $query->active();
            }])
            ->limit(6)
            ->get();

        return view('home', compact('featuredProducts', 'categories'));
    }
}
