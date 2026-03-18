@extends('layouts.app')

@section('title', 'My Wishlist - High Elevation Coffee')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <!-- Sidebar (desktop) -->
        <div class="hidden lg:block lg:col-span-3">
            @include('account.partials.sidebar')
        </div>

        <div class="lg:col-span-9">
            <!-- Mobile tab navigation -->
            <div class="lg:hidden mb-6 flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <a href="{{ route('account.dashboard') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('account.dashboard') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Dashboard</a>
                <a href="{{ route('orders.index') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('orders.*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Orders</a>
                <a href="{{ route('wishlist.index') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('wishlist.*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Wishlist</a>
                <a href="{{ route('account.addresses') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('account.addresses*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Addresses</a>
                <a href="{{ route('account.profile') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('account.profile*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Profile</a>
            </div>

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Wishlist</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }} saved</p>
                </div>
                @if($wishlistItems->isNotEmpty())
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-amber-600 hover:text-amber-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Browse More
                    </a>
                @endif
            </div>

            @if($wishlistItems->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="mx-auto w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Your wishlist is empty</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">Start adding your favorite coffees and products — they'll be waiting here for you.</p>
                    <a href="{{ route('products.index') }}" class="mt-6 inline-flex items-center gap-2 bg-amber-600 text-white px-6 py-2.5 rounded-lg hover:bg-amber-700 transition-colors text-sm font-medium shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Explore Products
                    </a>
                </div>
            @else
                <!-- Wishlist Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($wishlistItems as $item)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group hover:shadow-md transition-shadow duration-200" x-data="{ removing: false }">
                            <!-- Product Image -->
                            <a href="{{ route('products.show', $item->product->slug) }}" class="block relative overflow-hidden">
                                @if($item->product->primaryImage)
                                    <img src="{{ $item->product->primaryImage->image_url }}" alt="{{ $item->product->name }}"
                                         class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-52 bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif

                                {{-- Sale badge --}}
                                @if($item->product->is_on_sale)
                                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">SALE</span>
                                @endif

                                {{-- Stock badge --}}
                                @if(!$item->product->is_in_stock)
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                        <span class="bg-white/90 text-gray-900 text-sm font-semibold px-4 py-1.5 rounded-full">Out of Stock</span>
                                    </div>
                                @endif

                                {{-- Remove button (top-right) --}}
                                <form action="{{ route('wishlist.remove', $item->product->id) }}" method="POST"
                                      class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                      @submit="removing = true">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-sm hover:bg-red-50 hover:text-red-600 text-gray-500 transition-colors" title="Remove from wishlist">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </a>

                            <!-- Product Info -->
                            <div class="p-4" x-show="!removing" x-transition>
                                <a href="{{ route('products.show', $item->product->slug) }}" class="block">
                                    <h3 class="text-sm font-semibold text-gray-900 hover:text-amber-600 transition-colors line-clamp-2 leading-snug">{{ $item->product->name }}</h3>
                                </a>

                                @if($item->product->category)
                                    <p class="text-xs text-gray-400 mt-1">{{ $item->product->category->name }}</p>
                                @endif

                                {{-- Price --}}
                                <div class="mt-3 flex items-baseline gap-2">
                                    @if($item->product->is_on_sale)
                                        <span class="text-lg font-bold text-red-600">₱{{ number_format($item->product->sale_price, 2) }}</span>
                                        <span class="text-sm text-gray-400 line-through">₱{{ number_format($item->product->price, 2) }}</span>
                                        @php $discount = round((($item->product->price - $item->product->sale_price) / $item->product->price) * 100); @endphp
                                        <span class="text-xs font-medium text-red-500">-{{ $discount }}%</span>
                                    @else
                                        <span class="text-lg font-bold text-gray-900">₱{{ number_format($item->product->price, 2) }}</span>
                                    @endif
                                </div>

                                {{-- Add to Cart --}}
                                <div class="mt-4">
                                    @if($item->product->is_in_stock)
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-amber-600 text-white py-2.5 px-4 rounded-lg hover:bg-amber-700 active:bg-amber-800 transition-colors text-sm font-medium shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                                Add to Cart
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="w-full flex items-center justify-center gap-2 bg-gray-100 text-gray-400 py-2.5 px-4 rounded-lg text-sm font-medium cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Out of Stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
