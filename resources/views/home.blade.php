@extends('layouts.app')

@section('title', 'High Elevation Coffee — Premium Beans, Freshly Roasted')
@section('description', 'Discover the finest coffee beans from around the world. Premium quality, freshly roasted, and delivered to your door.')

@section('content')
    {{-- ══════════════════════════════════════════ --}}
    {{--  HERO                                      --}}
    {{-- ══════════════════════════════════════════ --}}
    <section class="relative min-h-[92vh] flex items-center overflow-hidden bg-gray-950">
        {{-- Background image --}}
        <div class="absolute inset-0">
            <img src="/images/hero-coffee-beans.jpg" alt="" class="w-full h-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-950/80 via-gray-950/50 to-transparent"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 bg-amber-600/20 text-amber-400 text-xs font-semibold tracking-widest uppercase px-4 py-1.5 rounded-full border border-amber-500/30 mb-6">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Freshly Roasted &amp; Shipped Daily
                </span>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
                    Premium Coffee
                    <span class="block text-amber-400 mt-1">Elevated Experience</span>
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-gray-300 leading-relaxed max-w-lg">
                    Hand-selected single-origin beans and artisan blends, roasted to perfection and delivered fresh to your doorstep.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center justify-center gap-2 bg-amber-600 text-white px-7 py-3.5 rounded-xl font-semibold hover:bg-amber-500 transition-colors shadow-lg shadow-amber-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Shop All Coffee
                    </a>
                    <a href="{{ route('products.index', ['featured' => 1]) }}"
                       class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur text-white border border-white/20 px-7 py-3.5 rounded-xl font-semibold hover:bg-white/20 transition-colors">
                        Featured Blends
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </div>
    </section>

    {{-- ══════════════════════════════════════════ --}}
    {{--  TRUST BAR                                 --}}
    {{-- ══════════════════════════════════════════ --}}
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">100% Arabica</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Roasted Fresh</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Free Shipping ₱2k+</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Satisfaction Guaranteed</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════ --}}
    {{--  FEATURED PRODUCTS                         --}}
    {{-- ══════════════════════════════════════════ --}}
    <section class="py-16 lg:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Featured Selections</h2>
                    <p class="mt-2 text-gray-500">Handpicked by our master roasters for an exceptional cup.</p>
                </div>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 hover:text-amber-700 transition-colors group">
                    View all
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            @if($featuredProducts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                            {{-- Image --}}
                            <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden">
                                <img src="{{ $product->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300">

                                @if($product->is_on_sale)
                                    <span class="absolute top-3 left-3 bg-red-500 text-white text-[11px] font-bold px-2.5 py-1 rounded-full shadow">SALE</span>
                                @endif

                                @if(!$product->is_in_stock)
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                        <span class="bg-white/90 text-gray-900 text-sm font-semibold px-4 py-1.5 rounded-full">Sold Out</span>
                                    </div>
                                @endif
                            </a>

                            {{-- Details --}}
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-medium text-amber-600 uppercase tracking-wide">{{ $product->category?->name }}</span>
                                    @if($product->roast_level)
                                        <span class="text-[11px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full capitalize">{{ str_replace('_', ' ', $product->roast_level) }}</span>
                                    @endif
                                </div>

                                <a href="{{ route('products.show', $product->slug) }}" class="block">
                                    <h3 class="font-semibold text-gray-900 leading-snug line-clamp-1 hover:text-amber-600 transition-colors">{{ $product->name }}</h3>
                                </a>

                                <p class="text-sm text-gray-500 line-clamp-2 mt-1 leading-relaxed">{{ $product->short_description }}</p>

                                {{-- Price + Cart --}}
                                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                    <div>
                                        @if($product->is_on_sale)
                                            <span class="text-lg font-bold text-red-600">₱{{ number_format($product->sale_price, 2) }}</span>
                                            <span class="text-sm text-gray-400 line-through ml-1">₱{{ number_format($product->price, 2) }}</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-900">₱{{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>

                                    @if($product->is_in_stock)
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="p-2.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-colors" title="Add to cart">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <h3 class="mt-3 text-sm font-medium text-gray-900">No featured products yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back soon for our curated picks.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ══════════════════════════════════════════ --}}
    {{--  SHOP BY CATEGORY                          --}}
    {{-- ══════════════════════════════════════════ --}}
    <section class="py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Shop by Category</h2>
                <p class="mt-2 text-gray-500 max-w-lg mx-auto">Explore our curated collections of the world's finest coffee beans.</p>
            </div>

            @if($categories->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categories as $category)
                        <a href="{{ route('products.category', $category->slug) }}"
                           class="group relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3] flex items-end">
                            {{-- Image --}}
                            <img src="{{ $category->image_url ?? '/images/category-placeholder.jpg' }}"
                                 alt="{{ $category->name }}"
                                 class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-50 group-hover:scale-105 transition-all duration-500">

                            {{-- Overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-gray-900/20 to-transparent"></div>

                            {{-- Text --}}
                            <div class="relative p-6 w-full">
                                <h3 class="text-xl font-bold text-white mb-1 group-hover:text-amber-300 transition-colors">{{ $category->name }}</h3>
                                @if($category->description)
                                    <p class="text-sm text-gray-300 line-clamp-2 mb-3">{{ $category->description }}</p>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-400 font-medium">{{ $category->active_products_count }} {{ Str::plural('product', $category->active_products_count) }}</span>
                                    <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-400 group-hover:gap-2 transition-all">
                                        Browse
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ══════════════════════════════════════════ --}}
    {{--  WHY HIGH ELEVATION                        --}}
    {{-- ══════════════════════════════════════════ --}}
    <section class="py-16 lg:py-20 bg-amber-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Why High Elevation?</h2>
                <p class="mt-2 text-gray-500 max-w-lg mx-auto">We source, roast, and deliver with one goal — the perfect cup.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="mx-auto w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Ethically Sourced</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">Direct partnerships with farmers across the world's premier coffee-growing regions.</p>
                </div>

                <div class="text-center">
                    <div class="mx-auto w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Small-Batch Roasted</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">Every batch is roasted to order, ensuring peak flavor and aroma in every bag.</p>
                </div>

                <div class="text-center">
                    <div class="mx-auto w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Fair Pricing</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">Premium quality without the premium markup. Great coffee should be accessible.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════ --}}
    {{--  CTA BANNER                                --}}
    {{-- ══════════════════════════════════════════ --}}
    <section class="bg-gray-900 py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-white">Ready to taste the difference?</h2>
            <p class="mt-3 text-gray-400 max-w-lg mx-auto">Join thousands of coffee lovers who start their mornings with High Elevation.</p>
            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center justify-center gap-2 bg-amber-600 text-white px-7 py-3.5 rounded-xl font-semibold hover:bg-amber-500 transition-colors shadow-lg shadow-amber-600/25">
                    Start Shopping
                </a>
                <a href="{{ route('about') }}"
                   class="inline-flex items-center justify-center gap-2 border border-gray-600 text-gray-300 px-7 py-3.5 rounded-xl font-semibold hover:bg-gray-800 transition-colors">
                    Our Story
                </a>
            </div>
        </div>
    </section>
@endsection