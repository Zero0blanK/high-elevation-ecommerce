@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
<div x-data="{
    selectAll: false,
    selectedIds: [],
    toggleAll() {
        this.selectedIds = this.selectAll ? {{ $products->pluck('id') }} : [];
    }
}">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                {{ $products->total() }}
            </span>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Product
        </a>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.products.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, SKU, description…" class="block w-full pl-9 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm">
                </div>
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="category" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm">
                    <option value="">All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div>
                <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                <select name="stock_status" id="stock_status" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm">
                    <option value="">All</option>
                    <option value="in_stock" @selected(request('stock_status') === 'in_stock')>In Stock</option>
                    <option value="low_stock" @selected(request('stock_status') === 'low_stock')>Low Stock</option>
                    <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>Out of Stock</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                    <svg class="inline h-4 w-4 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                <a href="{{ route('admin.products.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-4 py-2 rounded-lg transition-colors text-sm">Clear</a>
            </div>
        </div>
    </form>

    {{-- Floating Bulk Action Bar --}}
    <div x-show="selectedIds.length > 0" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40">
        <form method="POST" action="{{ route('admin.products.bulk-action') }}" class="flex items-center gap-3 bg-gray-900 text-white rounded-xl shadow-2xl px-5 py-3">
            @csrf
            <template x-for="id in selectedIds" :key="id">
                <input type="hidden" name="product_ids[]" :value="id">
            </template>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-amber-500 text-xs font-bold" x-text="selectedIds.length"></span>
                <span class="text-sm font-medium">selected</span>
            </div>
            <div class="h-5 w-px bg-gray-600"></div>
            <select name="action" required class="bg-gray-800 border-gray-600 text-white rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500 py-1.5">
                <option value="">Choose action…</option>
                <option value="activate">Activate</option>
                <option value="deactivate">Deactivate</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" onclick="return confirm('Are you sure you want to perform this bulk action?')" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-1.5 rounded-lg transition-colors text-sm">
                Apply
            </button>
            <button type="button" @click="selectedIds = []; selectAll = false" class="text-gray-400 hover:text-white transition-colors ml-1">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </form>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" value="{{ $product->id }}" x-model.number="selectedIds" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($product->primaryImage)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $product->primaryImage->image_url) }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.products.show', $product) }}" class="text-sm font-semibold text-gray-900 hover:text-amber-600 transition-colors truncate block">{{ $product->name }}</a>
                                        <p class="text-xs text-gray-500 font-mono">{{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600">{{ $product->category?->name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($product->compare_price && $product->compare_price > $product->price)
                                    <span class="text-sm font-semibold text-gray-900">₱{{ number_format($product->price, 2) }}</span>
                                    <span class="text-xs text-gray-400 line-through block">₱{{ number_format($product->compare_price, 2) }}</span>
                                @else
                                    <span class="text-sm font-semibold text-gray-900">₱{{ number_format($product->price, 2) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($product->stock_quantity <= 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">Out of Stock</span>
                                @elseif($product->stock_quantity <= ($product->low_stock_threshold ?? 10))
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20">Low ({{ $product->stock_quantity }})</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">{{ $product->stock_quantity }} in stock</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/20">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.products.show', $product) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="View">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete &quot;{{ $product->name }}&quot;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">No products found</p>
                                    <p class="text-sm text-gray-500 mb-4">Get started by adding your first product.</p>
                                    <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        Add Product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Mobile Card Layout --}}
    <div class="md:hidden space-y-3">
        @forelse($products as $product)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-start gap-3">
                    <input type="checkbox" value="{{ $product->id }}" x-model.number="selectedIds" class="mt-1 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                    <div class="h-14 w-14 flex-shrink-0">
                        @if($product->primaryImage)
                            <img class="h-14 w-14 rounded-lg object-cover" src="{{ asset('storage/' . $product->primaryImage->image_url) }}" alt="{{ $product->name }}">
                        @else
                            <div class="h-14 w-14 rounded-lg bg-gray-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <a href="{{ route('admin.products.show', $product) }}" class="text-sm font-semibold text-gray-900 hover:text-amber-600 transition-colors">{{ $product->name }}</a>
                                <p class="text-xs text-gray-500 font-mono">{{ $product->sku }}</p>
                            </div>
                            @if($product->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/20">Inactive</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-sm">
                            <span class="font-semibold text-gray-900">₱{{ number_format($product->price, 2) }}</span>
                            <span class="text-gray-500">{{ $product->category?->name ?? '—' }}</span>
                            @if($product->stock_quantity <= 0)
                                <span class="text-xs font-medium text-red-600">Out of Stock</span>
                            @elseif($product->stock_quantity <= ($product->low_stock_threshold ?? 10))
                                <span class="text-xs font-medium text-yellow-600">Low ({{ $product->stock_quantity }})</span>
                            @else
                                <span class="text-xs text-gray-500">{{ $product->stock_quantity }} in stock</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ route('admin.products.show', $product) }}" class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 hover:text-amber-700">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete &quot;{{ $product->name }}&quot;?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-700">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-900 mb-1">No products found</p>
                <p class="text-sm text-gray-500 mb-4">Get started by adding your first product.</p>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Product
                </a>
            </div>
        @endforelse

        @if($products->hasPages())
            <div class="mt-4">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection