@extends('layouts.app')

@section('title', 'All Coffee Products')
@section('description', 'Browse our complete selection of premium coffee beans, single origins, and expertly crafted blends.')

@section('content')
<div class="bg-white mt-16">
    <!-- Header -->
    <div class="bg-gray-50 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900">All Coffee Products</h1>
            <p class="mt-2 text-gray-600">Discover our complete selection of premium coffee beans</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            <!-- Filters Sidebar -->
            <div class="hidden lg:block">
                <div class="bg-white border rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Filters</h3>
                    
                    <form method="GET" action="{{ route('products.index') }}">
                        <!-- Categories -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Categories</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="category" value="" 
                                           @if(!request('category')) checked @endif
                                           class="text-amber-600 focus:ring-amber-500">
                                    <span class="ml-2 text-sm text-gray-700">All Categories</span>
                                </label>
                                @foreach($categories as $category)
                                    <label class="flex items-center">
                                        <input type="radio" name="category" value="{{ $category->id }}" 
                                               @if(request('category') == $category->id) checked @endif
                                               class="text-amber-600 focus:ring-amber-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Roast Level -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Roast Level</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="roast_level" value="" 
                                           @if(!request('roast_level')) checked @endif
                                           class="text-amber-600 focus:ring-amber-500">
                                    <span class="ml-2 text-sm text-gray-700">All Roasts</span>
                                </label>
                                @foreach(['light', 'medium', 'dark', 'extra_dark'] as $roast)
                                    <label class="flex items-center">
                                        <input type="radio" name="roast_level" value="{{ $roast }}" 
                                               @if(request('roast_level') == $roast) checked @endif
                                               class="text-amber-600 focus:ring-amber-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $roast)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-amber-600 text-white py-2 px-4 rounded-md hover:bg-amber-700 transition-colors">
                            Apply Filters
                        </button>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="lg:col-span-3">
                <!-- Sort and View Options -->
                <div class="flex items-center justify-between mb-6">
                    <p class="text-sm text-gray-700">
                        Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
                    </p>
                    
                    <form method="GET" action="{{ route('products.index') }}" class="flex items-center space-x-4">
                        @foreach(request()->except(['sort', 'direction']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        
                        <select name="sort" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm">
                            <option value="name" @if(request('sort') == 'name') selected @endif>Name</option>
                            <option value="price" @if(request('sort') == 'price') selected @endif>Price</option>
                            <option value="created_at" @if(request('sort') == 'created_at') selected @endif>Newest</option>
                        </select>
                    </form>
                </div>

                <!-- Products -->
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                                <div class="aspect-w-1 aspect-h-1">
                                    <img src="{{ $product->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}" 
                                          alt="{{ $product->name }}" 
                                          class="w-full h-64 object-cover">
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm text-gray-500">{{ $product->category?->name }}</span>
                                        @if($product->isOnSale)
                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Sale</span>
                                        @endif
                                    </div>
                                    <h3 class="font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->short_description }}</p>
                                    
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            @if($product->isOnSale)
                                                <span class="text-lg font-bold text-red-600">${{ number_format($product->sale_price, 2) }}</span>
                                                <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                                            @else
                                                <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                            @endif
                                        </div>
                                        <span class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $product->roast_level)) }}</span>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" 
                                                    @class([
                                                        'w-full px-4 py-2 rounded-md font-medium transition-colors',
                                                        'bg-amber-600 text-white hover:bg-amber-700' => $product->isInStock,
                                                        'bg-gray-300 text-gray-500 cursor-not-allowed' => !$product->isInStock
                                                    ])
                                                    @if(!$product->isInStock) disabled @endif>
                                                @if($product->isInStock)
                                                    Add to Cart
                                                @else
                                                    Out of Stock
                                                @endif
                                            </button>
                                        </form>
                                        <a href="{{ route('products.show', $product->slug) }}" 
                                            class="px-3 py-2 text-amber-600 border border-amber-600 rounded-md hover:bg-amber-50 transition-colors">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @else
                    <!-- No Products Found -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4.5"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search terms.</p>
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                                View All Products
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection