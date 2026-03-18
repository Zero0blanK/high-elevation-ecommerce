@extends('layouts.app')

@section('title', 'Shop Coffee — High Elevation')
@section('description', 'Browse our complete selection of premium coffee beans, single origins, and expertly crafted blends.')

@section('content')
<div class="bg-gray-50 min-h-screen">
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Home</a>
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900 font-medium">Shop</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        @if(request('category'))
                            {{ $categories->firstWhere('id', request('category'))?->name ?? 'All Coffee' }}
                        @elseif(request('featured'))
                            Featured Blends
                        @else
                            All Coffee
                        @endif
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $products->total() }} {{ Str::plural('product', $products->total()) }} available</p>
                </div>

                {{-- Sort --}}
                <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">
                    @foreach(request()->except(['sort','direction','page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="sort" class="text-sm text-gray-500 whitespace-nowrap hidden sm:inline">Sort by</label>
                    <select name="sort" id="sort" onchange="this.form.submit()"
                            class="text-sm border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 bg-white pl-3 pr-8 py-2">
                        <option value="created_at" @selected(request('sort') == 'created_at')>Newest</option>
                        <option value="name" @selected(request('sort') == 'name')>Name A–Z</option>
                        <option value="price" @selected(request('sort') == 'price')>Price: Low → High</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">

            {{-- ─── Desktop Filter Sidebar ─── --}}
            <aside class="hidden lg:block">
                <div class="sticky top-24 space-y-6">
                    <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if(request('featured'))
                            <input type="hidden" name="featured" value="{{ request('featured') }}">
                        @endif

                        {{-- Categories --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Category</h3>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="radio" name="category" value="" @checked(!request('category'))
                                           class="text-amber-600 focus:ring-amber-500 w-4 h-4"
                                           onchange="document.getElementById('filterForm').submit()">
                                    <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">All Categories</span>
                                </label>
                                @foreach($categories as $category)
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="radio" name="category" value="{{ $category->id }}" @checked(request('category') == $category->id)
                                               class="text-amber-600 focus:ring-amber-500 w-4 h-4"
                                               onchange="document.getElementById('filterForm').submit()">
                                        <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Roast Level --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Roast Level</h3>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="radio" name="roast_level" value="" @checked(!request('roast_level'))
                                           class="text-amber-600 focus:ring-amber-500 w-4 h-4"
                                           onchange="document.getElementById('filterForm').submit()">
                                    <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">All Roasts</span>
                                </label>
                                @foreach(['light' => '☀️ Light', 'medium' => '🔥 Medium', 'dark' => '🌑 Dark', 'extra_dark' => '⬛ Extra Dark'] as $value => $label)
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="radio" name="roast_level" value="{{ $value }}" @checked(request('roast_level') == $value)
                                               class="text-amber-600 focus:ring-amber-500 w-4 h-4"
                                               onchange="document.getElementById('filterForm').submit()">
                                        <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Clear Filters --}}
                        @if(request()->hasAny(['category','roast_level','featured','search']))
                            <a href="{{ route('products.index') }}"
                               class="flex items-center justify-center gap-2 w-full py-2.5 text-sm font-medium text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Clear All Filters
                            </a>
                        @endif
                    </form>
                </div>
            </aside>

            {{-- ─── Mobile Filter Toggle ─── --}}
            <div class="lg:hidden mb-6" x-data="{ filtersOpen: false }">
                <button @click="filtersOpen = !filtersOpen"
                        class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filters
                    @if(request()->hasAny(['category','roast_level']))
                        <span class="bg-amber-600 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[1.25rem] text-center">{{ collect([request('category'), request('roast_level')])->filter()->count() }}</span>
                    @endif
                </button>

                <div x-show="filtersOpen" x-transition x-cloak class="mt-3 bg-white rounded-xl border border-gray-200 p-5 space-y-5">
                    <form method="GET" action="{{ route('products.index') }}" id="mobileFilterForm">
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Category</h4>
                            <div class="flex flex-wrap gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="category" value="" @checked(!request('category')) class="sr-only peer"
                                           onchange="document.getElementById('mobileFilterForm').submit()">
                                    <span class="inline-block px-3 py-1.5 text-sm rounded-full border border-gray-200 peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-600 hover:bg-gray-50 transition-colors">All</span>
                                </label>
                                @foreach($categories as $category)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="category" value="{{ $category->id }}" @checked(request('category') == $category->id) class="sr-only peer"
                                               onchange="document.getElementById('mobileFilterForm').submit()">
                                        <span class="inline-block px-3 py-1.5 text-sm rounded-full border border-gray-200 peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-600 hover:bg-gray-50 transition-colors">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Roast</h4>
                            <div class="flex flex-wrap gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="roast_level" value="" @checked(!request('roast_level')) class="sr-only peer"
                                           onchange="document.getElementById('mobileFilterForm').submit()">
                                    <span class="inline-block px-3 py-1.5 text-sm rounded-full border border-gray-200 peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-600 hover:bg-gray-50 transition-colors">All</span>
                                </label>
                                @foreach(['light' => 'Light', 'medium' => 'Medium', 'dark' => 'Dark', 'extra_dark' => 'Extra Dark'] as $val => $lbl)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="roast_level" value="{{ $val }}" @checked(request('roast_level') == $val) class="sr-only peer"
                                               onchange="document.getElementById('mobileFilterForm').submit()">
                                        <span class="inline-block px-3 py-1.5 text-sm rounded-full border border-gray-200 peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-600 hover:bg-gray-50 transition-colors">{{ $lbl }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ─── Product Grid ─── --}}
            <div class="lg:col-span-3">
                @if($products->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden group hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                                {{-- Image --}}
                                <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden aspect-[4/5]">
                                    <img src="{{ $product->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy">

                                    @if($product->is_on_sale)
                                        @php $disc = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
                                        <span class="absolute top-2.5 left-2.5 bg-red-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full shadow-sm">-{{ $disc }}%</span>
                                    @endif

                                    @if(!$product->is_in_stock)
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                            <span class="bg-white/90 text-gray-900 text-xs sm:text-sm font-semibold px-3 py-1 rounded-full">Sold Out</span>
                                        </div>
                                    @endif

                                    {{-- Quick add overlay --}}
                                    @if($product->is_in_stock)
                                        <div class="absolute inset-x-0 bottom-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-200">
                                            <button type="button"
                                                    onclick="addToCart(event, {{ $product->id }}, 1)"
                                                    class="w-full flex items-center justify-center gap-2 bg-amber-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors shadow-lg shadow-black/20">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                                Quick Add
                                            </button>
                                        </div>
                                    @endif
                                </a>

                                {{-- Details --}}
                                <div class="p-3 sm:p-4">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11px] sm:text-xs font-medium text-amber-600 uppercase tracking-wide">{{ $product->category?->name }}</span>
                                        @if($product->roast_level)
                                            <span class="text-[10px] sm:text-[11px] text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded capitalize hidden sm:inline">{{ str_replace('_', ' ', $product->roast_level) }}</span>
                                        @endif
                                    </div>

                                    <a href="{{ route('products.show', $product->slug) }}" class="block">
                                        <h3 class="text-sm sm:text-base font-semibold text-gray-900 leading-snug line-clamp-2 hover:text-amber-600 transition-colors">{{ $product->name }}</h3>
                                    </a>

                                    <p class="text-xs sm:text-sm text-gray-500 line-clamp-1 mt-1 hidden sm:block">{{ $product->short_description }}</p>

                                    {{-- Price --}}
                                    <div class="mt-3 pt-2.5 border-t border-gray-100">
                                        @if($product->is_on_sale)
                                            <div class="flex items-baseline gap-1.5">
                                                <span class="text-base sm:text-lg font-bold text-red-600">₱{{ number_format($product->sale_price, 2) }}</span>
                                                <span class="text-xs sm:text-sm text-gray-400 line-through">₱{{ number_format($product->price, 2) }}</span>
                                            </div>
                                        @else
                                            <span class="text-base sm:text-lg font-bold text-gray-900">₱{{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($products->hasPages())
                        <div class="mt-10">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    {{-- No Products --}}
                    <div class="text-center py-20 bg-white rounded-xl border border-gray-200">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">No products found</h3>
                        <p class="mt-1.5 text-sm text-gray-500 max-w-sm mx-auto">Try adjusting your filters or browse all products.</p>
                        <a href="{{ route('products.index') }}" class="mt-5 inline-flex items-center gap-2 bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors">
                            View All Products
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Toast notification --}}
<div id="toast-notification" class="fixed bottom-4 right-4 z-50 hidden transform transition-all duration-300 translate-y-full opacity-0">
    <div class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span id="toast-message">Product added to cart!</span>
    </div>
</div>

@push('scripts')
<script>
function addToCart(event, productId, quantity) {
    // Prevent the click from propagating to the parent anchor tag
    event.preventDefault();
    event.stopPropagation();
    
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Product added to cart!');
            updateCartCount(data.cart_count);
        } else {
            showToast(data.message || 'Failed to add product', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

function updateCartCount(count) {
    const badge = document.getElementById('cart-count-badge');
    if (badge) {
        badge.textContent = count;
        badge.setAttribute('data-cart-count', count);
        if (count > 0) {
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast-notification');
    const toastMessage = document.getElementById('toast-message');
    const toastDiv = toast.querySelector('div');
    
    toastMessage.textContent = message;
    toastDiv.className = type === 'error' 
        ? 'bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3'
        : 'bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3';
    
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.remove('translate-y-full', 'opacity-0');
    }, 10);
    
    setTimeout(() => {
        toast.classList.add('translate-y-full', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 300);
    }, 3000);
}
</script>
@endpush
@endsection