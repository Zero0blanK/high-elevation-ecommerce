@extends('layouts.app')

@section('title', $product->meta_title ?: $product->name . ' — High Elevation Coffee')
@section('description', $product->meta_description ?: $product->short_description)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Home</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('products.index') }}" class="hover:text-amber-600 transition-colors">Shop</a>
            @if($product->category)
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('products.category', $product->category->slug) }}" class="hover:text-amber-600 transition-colors">{{ $product->category->name }}</a>
            @endif
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium truncate max-w-[200px]">{{ $product->name }}</span>
        </nav>

        {{-- Product Section --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="lg:grid lg:grid-cols-2">
                {{-- ─── Image Gallery ─── --}}
                <div class="relative bg-gray-100" x-data="{ current: 0 }">
                    {{-- Main Image --}}
                    <div class="aspect-square overflow-hidden">
                        @if($product->images && $product->images->count() > 0)
                            @foreach($product->images as $index => $image)
                                <img x-show="current === {{ $index }}"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     src="{{ $image->image_url }}"
                                     alt="{{ $image->alt_text ?? $product->name }}"
                                     class="w-full h-full object-cover">
                            @endforeach
                        @else
                            <img src="{{ $product->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                        @endif
                    </div>

                    {{-- Badges --}}
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        @if($product->is_on_sale)
                            @php $discount = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
                            <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow">-{{ $discount }}% OFF</span>
                        @endif
                        @if(!$product->is_in_stock)
                            <span class="bg-gray-900 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow">SOLD OUT</span>
                        @elseif($product->stock_quantity <= 10 && $product->stock_quantity > 0)
                            <span class="bg-amber-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow">Only {{ $product->stock_quantity }} left</span>
                        @endif
                    </div>

                    {{-- Wishlist button --}}
                    @auth('customer')
                        <form action="{{ route('wishlist.toggle') }}" method="POST" class="absolute top-4 right-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="p-2.5 bg-white/90 backdrop-blur rounded-full shadow-sm hover:bg-red-50 transition-colors group" title="Add to wishlist">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>
                        </form>
                    @endauth

                    {{-- Thumbnail Strip --}}
                    @if($product->images && $product->images->count() > 1)
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 bg-white/80 backdrop-blur rounded-full px-3 py-2 shadow">
                            @foreach($product->images as $index => $image)
                                <button @click="current = {{ $index }}"
                                        :class="current === {{ $index }} ? 'ring-2 ring-amber-500 ring-offset-1' : 'opacity-60 hover:opacity-100'"
                                        class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 transition-all">
                                    <img src="{{ $image->image_url }}" alt="" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ─── Product Info ─── --}}
                <div class="p-6 sm:p-8 lg:p-10 flex flex-col" x-data="addToCartForm()">
                    {{-- Category --}}
                    @if($product->category)
                        <a href="{{ route('products.category', $product->category->slug) }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 uppercase tracking-wider hover:text-amber-700 transition-colors mb-3 w-fit">
                            {{ $product->category->name }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @endif

                    {{-- Name --}}
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h1>

                    {{-- Price --}}
                    <div class="mt-4 flex items-baseline gap-3">
                        @if($product->is_on_sale && $product->sale_price)
                            <span class="text-3xl font-bold text-red-600">₱{{ number_format($product->sale_price, 2) }}</span>
                            <span class="text-xl text-gray-400 line-through">₱{{ number_format($product->price, 2) }}</span>
                            <span class="text-sm font-medium text-red-500 bg-red-50 px-2.5 py-0.5 rounded-full">Save ₱{{ number_format($product->price - $product->sale_price, 2) }}</span>
                        @else
                            <span class="text-3xl font-bold text-gray-900">₱{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>

                    {{-- Short Description --}}
                    @if($product->short_description)
                        <p class="mt-4 text-gray-600 leading-relaxed">{{ $product->short_description }}</p>
                    @endif

                    {{-- Specs --}}
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        @if($product->roast_level)
                            <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400 uppercase tracking-wider">Roast</p>
                                    <p class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $product->roast_level) }}</p>
                                </div>
                            </div>
                        @endif
                        @if($product->grind_type)
                            <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400 uppercase tracking-wider">Grind</p>
                                    <p class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $product->grind_type) }}</p>
                                </div>
                            </div>
                        @endif
                        @if($product->weight)
                            <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400 uppercase tracking-wider">Weight</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $product->weight }} g</p>
                                </div>
                            </div>
                        @endif
                        @if($product->origin)
                            <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400 uppercase tracking-wider">Origin</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $product->origin }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Stock Status --}}
                    <div class="mt-5">
                        @if($product->stock_quantity > 10)
                            <div class="flex items-center gap-2 text-green-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span class="text-sm font-medium">In Stock</span>
                            </div>
                        @elseif($product->stock_quantity > 0)
                            <div class="flex items-center gap-2 text-amber-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <span class="text-sm font-medium">Low Stock — only {{ $product->stock_quantity }} left</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-red-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                <span class="text-sm font-medium">Out of Stock</span>
                            </div>
                        @endif
                    </div>

                    {{-- Add to Cart --}}
                    <div class="mt-6 space-y-3 flex-grow flex flex-col justify-end">
                        {{-- Quantity --}}
                        @if($product->is_in_stock)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <div class="inline-flex items-center bg-gray-100 rounded-xl">
                                    <button type="button" @click="decreaseQuantity()"
                                            :disabled="quantity <= 1"
                                            class="px-3.5 py-2.5 text-gray-600 hover:text-gray-900 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <input type="number" x-model="quantity" min="1" max="{{ $product->stock_quantity }}"
                                           @input="validateQuantity()"
                                           class="w-14 text-center bg-transparent border-0 text-sm font-semibold focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    <button type="button" @click="increaseQuantity()"
                                            :disabled="quantity >= maxQuantity"
                                            class="px-3.5 py-2.5 text-gray-600 hover:text-gray-900 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex gap-3">
                            <button type="button"
                                    @click="addToCartAjax()"
                                    :disabled="!inStock || isSubmitting"
                                    :class="!inStock ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-amber-600 text-white hover:bg-amber-700 shadow-lg shadow-amber-600/20'"
                                    class="flex-1 flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl font-semibold transition-colors">
                                <svg x-show="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                <svg x-show="isSubmitting" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmitting ? 'Adding...' : (inStock ? 'Add to Cart' : 'Out of Stock')"></span>
                            </button>

                            @if($product->is_in_stock)
                                <form action="{{ route('checkout.buyNow') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" x-model="quantity">
                                    <button type="submit"
                                            class="flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl font-semibold border-2 border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white transition-colors">
                                        Buy Now
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Trust Signals --}}
                    <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-center gap-2.5 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Free shipping on ₱2,000+
                        </div>
                        <div class="flex items-center gap-2.5 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Shipped within 24 hours
                        </div>
                        <div class="flex items-center gap-2.5 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Freshness guaranteed
                        </div>
                        <div class="flex items-center gap-2.5 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Secure checkout
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Full Description ─── --}}
        @if($product->description)
            <div class="mt-8 bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 mb-4">About This Coffee</h2>
                <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        @endif

        {{-- ─── Reviews ─── --}}
        <div class="mt-8 bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm">
            @include('reviews.product-reviews')
        </div>

        {{-- ─── Related Products ─── --}}
        @if($relatedProducts->count() > 0)
            <div class="mt-12 mb-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900">You Might Also Like</h2>
                    <a href="{{ route('products.index') }}" class="text-sm font-medium text-amber-600 hover:text-amber-700 transition-colors hidden sm:inline-flex items-center gap-1">
                        View all
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($relatedProducts as $related)
                        <a href="{{ route('products.show', $related->slug) }}"
                           class="bg-white rounded-xl border border-gray-200 overflow-hidden group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                            <div class="aspect-square overflow-hidden">
                                <img src="{{ $related->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}"
                                     alt="{{ $related->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            </div>
                            <div class="p-3 sm:p-4">
                                <h3 class="text-sm font-semibold text-gray-900 line-clamp-1 group-hover:text-amber-600 transition-colors">{{ $related->name }}</h3>
                                <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $related->short_description }}</p>
                                <div class="mt-2">
                                    @if($related->isOnSale())
                                        <span class="text-base font-bold text-red-600">₱{{ number_format($related->sale_price, 2) }}</span>
                                        <span class="text-xs text-gray-400 line-through ml-1">₱{{ number_format($related->price, 2) }}</span>
                                    @else
                                        <span class="text-base font-bold text-gray-900">₱{{ number_format($related->price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    function addToCartForm() {
        return {
            quantity: 1,
            maxQuantity: {{ $product->stock_quantity }},
            inStock: {{ $product->stock_quantity > 0 ? 'true' : 'false' }},
            isSubmitting: false,
            decreaseQuantity() {
                if (this.quantity > 1) this.quantity--;
            },
            increaseQuantity() {
                if (this.quantity < this.maxQuantity) this.quantity++;
            },
            validateQuantity() {
                this.quantity = Math.max(1, Math.min(this.quantity, this.maxQuantity));
            },
            handleSubmit(event) {
                if (!this.inStock || this.quantity <= 0) event.preventDefault();
            },
            addToCartAjax() {
                if (!this.inStock || this.isSubmitting) return;
                
                this.isSubmitting = true;
                
                fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: {{ $product->id }},
                        quantity: this.quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showToast(data.message || 'Added to cart!', 'success');
                        // Update cart count badge
                        const badge = document.getElementById('cart-count-badge');
                        if (badge && data.cart_count !== undefined) {
                            badge.textContent = data.cart_count;
                            badge.setAttribute('data-cart-count', data.cart_count);
                            if (data.cart_count > 0) {
                                badge.classList.remove('hidden');
                            }
                        }
                    } else {
                        this.showToast(data.message || 'Failed to add to cart', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.showToast('An error occurred. Please try again.', 'error');
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
            },
            showToast(message, type) {
                // Create toast element
                const toast = document.createElement('div');
                toast.className = `fixed bottom-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-y-full ${type === 'error' ? 'bg-red-600' : 'bg-green-600'} text-white flex items-center gap-3`;
                toast.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'error' ? 'M6 18L18 6M6 6l12 12' : 'M5 13l4 4L19 7'}"/>
                    </svg>
                    <span>${message}</span>
                `;
                document.body.appendChild(toast);
                
                setTimeout(() => toast.classList.remove('translate-y-full'), 10);
                setTimeout(() => {
                    toast.classList.add('translate-y-full');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        }
    }
</script>
@endsection