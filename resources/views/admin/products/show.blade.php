@extends('admin.layouts.app')

@section('title', $product->name)

@section('content')
<div x-data="{ showDeleteModal: false }">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center h-9 w-9 rounded-lg bg-white border border-gray-300 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-sm text-gray-500 font-mono">{{ $product->sku }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.edit', $product) }}" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <button @click="showDeleteModal = true" type="button" class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg transition-colors inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Image Gallery --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="{ activeImage: 0 }">
                @if($product->images->count())
                    <div class="mb-4">
                        <img :src="document.querySelectorAll('[data-gallery-src]')[activeImage]?.dataset.gallerySrc || '{{ asset('storage/' . $product->images->first()->image_url) }}'"
                             class="w-full h-80 object-cover rounded-xl" alt="{{ $product->name }}">
                    </div>
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-5 gap-3">
                            @foreach($product->images as $index => $image)
                                <button @click="activeImage = {{ $index }}" data-gallery-src="{{ asset('storage/' . $image->image_url) }}"
                                        class="aspect-square rounded-lg overflow-hidden border-2 transition-all focus:outline-none"
                                        :class="activeImage === {{ $index }} ? 'border-amber-500 ring-2 ring-amber-200' : 'border-transparent hover:border-gray-300'">
                                    <img src="{{ asset('storage/' . $image->image_url) }}" alt="{{ $image->alt_text ?? $product->name }}" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="h-80 bg-gray-50 rounded-xl flex flex-col items-center justify-center">
                        <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-sm text-gray-500">No images uploaded</p>
                    </div>
                @endif
            </div>

            {{-- Product Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-5">Product Details</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $product->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">SKU</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $product->sku }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Slug</dt>
                        <dd class="mt-1 text-sm text-gray-500 font-mono">{{ $product->slug }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Weight</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->weight ? $product->weight . 'g' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Origin</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->origin ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Roast Level</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->roast_level ? ucwords(str_replace(['-', '_'], ' ', $product->roast_level)) : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Grind Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->grind_type ? ucwords(str_replace(['-', '_'], ' ', $product->grind_type)) : '—' }}</dd>
                    </div>
                    @if($product->flavor_notes)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Flavor Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->flavor_notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Description --}}
            @if($product->description)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
                    <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-line">{{ $product->description }}</div>
                </div>
            @endif

            {{-- SEO --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-5">SEO Information</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Meta Title</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->meta_title ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Meta Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->meta_description ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            {{-- Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Visibility</span>
                        @if($product->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/20">Inactive</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Featured</span>
                        @if($product->is_featured)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">Yes</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/20">No</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Created</span>
                        <span class="text-sm text-gray-900">{{ $product->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Updated</span>
                        <span class="text-sm text-gray-900">{{ $product->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Pricing</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Price</span>
                        <span class="text-lg font-bold text-gray-900">₱{{ number_format($product->price, 2) }}</span>
                    </div>
                    @if($product->compare_price)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Compare Price</span>
                            <span class="text-sm text-gray-400 line-through">₱{{ number_format($product->compare_price, 2) }}</span>
                        </div>
                    @endif
                    @if($product->cost_price)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Cost Price</span>
                            <span class="text-sm text-gray-900">₱{{ number_format($product->cost_price, 2) }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">Profit Margin</span>
                                <span class="text-sm font-semibold text-green-600">₱{{ number_format($product->price - $product->cost_price, 2) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Stock --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Inventory</h2>
                <div class="space-y-4">
                    {{-- Stock Level Visual --}}
                    <div>
                        @php
                            $stockPercent = $product->low_stock_threshold > 0 ? min(100, ($product->stock_quantity / max($product->low_stock_threshold * 3, 1)) * 100) : ($product->stock_quantity > 0 ? 100 : 0);
                            $stockColor = $product->stock_quantity <= 0 ? 'red' : ($product->stock_quantity <= ($product->low_stock_threshold ?? 10) ? 'yellow' : 'green');
                        @endphp
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-3xl font-bold text-gray-900">{{ $product->stock_quantity }}</span>
                            @if($product->stock_quantity <= 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">Out of Stock</span>
                            @elseif($product->stock_quantity <= ($product->low_stock_threshold ?? 10))
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20">Low Stock</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">In Stock</span>
                            @endif
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-{{ $stockColor }}-500 h-2 rounded-full transition-all" style="width: {{ $stockPercent }}%"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Low Stock Threshold</span>
                        <span class="text-gray-900 font-medium">{{ $product->low_stock_threshold ?? '—' }}</span>
                    </div>
                    {{-- Quick Stock Update --}}
                    <div class="pt-3 border-t border-gray-100">
                        <form method="POST" action="{{ route('admin.products.update-stock', $product) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" min="0" class="flex-1 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm text-sm">
                            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-3 py-2 rounded-lg transition-colors text-sm">
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Category --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Category</h2>
                @if($product->category)
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-amber-50 flex items-center justify-center">
                            <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $product->category->name }}</span>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No category assigned</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Inventory Logs --}}
    @if($product->inventoryLogs && $product->inventoryLogs->count())
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Inventory Logs</h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                        {{ $product->inventoryLogs->count() }} entries
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Before</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Change</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">After</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($product->inventoryLogs->take(10) as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $log->type === 'addition' ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : ($log->type === 'subtraction' ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20' : 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20') }}">
                                        {{ ucfirst($log->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-900 text-right font-mono">{{ $log->quantity_before }}</td>
                                <td class="px-6 py-3 text-sm text-right font-mono font-semibold {{ $log->quantity_changed >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $log->quantity_changed >= 0 ? '+' : '' }}{{ $log->quantity_changed }}
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-900 text-right font-mono font-semibold">{{ $log->quantity_after }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500 max-w-xs truncate">{{ $log->notes ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Delete Product</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Are you sure you want to delete <strong>{{ $product->name }}</strong>? This action cannot be undone and all associated data will be permanently removed.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                            Delete Product
                        </button>
                    </form>
                    <button type="button" @click="showDeleteModal = false" class="mt-3 sm:mt-0 w-full sm:w-auto bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-4 py-2 rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
