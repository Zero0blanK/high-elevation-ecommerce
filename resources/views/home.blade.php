@extends('layouts.app')

@section('title', 'Premium Coffee Beans - Freshly Roasted & Delivered')
@section('description', 'Discover the finest coffee beans from around the world. Premium quality, freshly roasted, and delivered to your door.')

@section('content')
  <!-- Hero Section -->
  <section class="relative bg-gradient-to-br from-amber-50 to-orange-100 overflow-hidden">
      <div class="absolute inset-0">
          <img src="/images/hero-coffee-beans.jpg" alt="Premium Coffee Beans" class="w-full h-full object-cover opacity-20">
      </div>
      <div class="relative flex flex-col items-center justify-center h-screen max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
          <div class="text-center">
              <h1 class="text-4xl sm:text-6xl font-bold text-gray-900 mb-6">
                  Premium Coffee Beans
                  <span class="text-amber-600">Delivered Fresh</span>
              </h1>
              <p class="text-xl text-gray-700 max-w-3xl mx-auto mb-8">
                  Experience the perfect cup with our carefully curated selection of single-origin and 
                  expertly blended coffee beans, roasted to perfection and delivered fresh to your door.
              </p>
              <div class="flex flex-col sm:flex-row gap-4 justify-center">
                  <a href="{{ route('products.index') }}" class="bg-amber-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-amber-700 transition-colors">
                      Shop All Coffee
                  </a>
                  <a href="{{ route('products.index', ['featured' => 1]) }}" class="bg-white text-amber-600 border-2 border-amber-600 px-8 py-3 rounded-lg font-semibold hover:bg-amber-50 transition-colors">
                      Featured Blends
                  </a>
              </div>
          </div>
      </div>
  </section>
  
  <!-- Featured Products -->
  <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
              <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Coffee Beans</h2>
              <p class="text-gray-600 max-w-2xl mx-auto">
                  Handpicked selections from our master roasters, featuring the finest beans from around the world.
              </p>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
              @forelse($featuredProducts as $product)
                  <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                      <div class="aspect-w-1 aspect-h-1">
                          <img src="{{ $product->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}" 
                                alt="{{ $product->name }}" 
                                class="w-full h-64 object-cover">
                      </div>
                      <div class="p-4">
                          <div class="flex items-center justify-between mb-2">
                              <span class="text-sm text-gray-500">{{ $product->category?->name }}</span>
                              @if($product->is_on_sale)
                                  <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Sale</span>
                              @endif
                          </div>
                          <h3 class="font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                          <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->short_description }}</p>
                          
                          <div class="flex items-center justify-between mb-3">
                              <div class="flex items-center space-x-2">
                                  @if($product->is_on_sale)
                                      <span class="text-lg font-bold text-red-600">${{ number_format($product->sale_price, 2) }}</span>
                                      <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                                  @else
                                      <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                  @endif
                              </div>
                              <div class="flex items-center space-x-1">
                                  <span class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $product->roast_level)) }}</span>
                              </div>
                          </div>
                          
                          <div class="flex gap-2">
                              <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                  @csrf
                                  <input type="hidden" name="product_id" value="{{ $product->id }}">
                                  <input type="hidden" name="quantity" value="1">
                                  <button type="submit" 
                                          @class([
                                              'w-full px-4 py-2 rounded-md font-medium transition-colors',
                                              'bg-amber-600 text-white hover:bg-amber-700' => $product->is_in_stock,
                                              'bg-gray-300 text-gray-500 cursor-not-allowed' => !$product->is_in_stock
                                          ])
                                          @if(!$product->is_in_stock) disabled @endif>
                                      @if($product->is_in_stock)
                                          Add to Cart
                                      @else
                                          Out of Stock
                                      @endif
                                  </button>
                              </form>
                              <a href="{{ route('products.show', $product->slug) }}" 
                                  class="px-3 py-2 text-amber-600 border border-amber-600 rounded-md hover:bg-amber-50 transition-colors">
                                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                  </svg>
                              </a>
                          </div>
                      </div>
                  </div>
              @empty
                  <div class="col-span-full text-center py-12">
                      <div class="text-gray-500">
                          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4.5"/>
                          </svg>
                          <h3 class="mt-2 text-sm font-medium text-gray-900">No featured products</h3>
                          <p class="mt-1 text-sm text-gray-500">Get started by adding some featured products.</p>
                      </div>
                  </div>
              @endforelse
          </div>
          
          <div class="text-center mt-8">
              <a href="{{ route('products.index') }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 font-medium">
                  View All Products
                  <svg class="ml-1 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                  </svg>
              </a>
          </div>
      </div>
  </section>
  
  <!-- Categories -->
  <section class="py-16 bg-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
              <h2 class="text-3xl font-bold text-gray-900 mb-4">Shop by Category</h2>
              <p class="text-gray-600">Explore our carefully curated selection of coffee beans</p>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
              @foreach($categories as $category)
                  <a href="{{ route('products.category', $category->slug) }}" 
                      class="group relative bg-white rounded-lg shadow-md hover:shadow-lg transition-all overflow-hidden">
                      <div class="aspect-w-16 aspect-h-9">
                          <img src="{{ $category->image_url ?? '/images/category-placeholder.jpg' }}" 
                                alt="{{ $category->name }}" 
                                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                      </div>
                      <div class="p-6">
                          <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-amber-600 transition-colors">
                              {{ $category->name }}
                          </h3>
                          <p class="text-gray-600 text-sm mb-4">{{ $category->description }}</p>
                          <div class="flex items-center justify-between">
                              <span class="text-sm text-gray-500">{{ $category->active_products_count }} products</span>
                              <svg class="h-5 w-5 text-amber-600 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                  <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                              </svg>
                          </div>
                      </div>
                  </a>
              @endforeach
          </div>
      </div>
  </section>
@endsection