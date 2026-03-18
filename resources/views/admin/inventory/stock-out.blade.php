@extends('admin.layouts.app')

@section('title', 'Stock Out — Remove Inventory')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Page Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.inventory.index') }}" class="inline-flex items-center text-sm font-medium text-amber-600 hover:text-amber-700 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Inventory
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Stock Out — Remove Inventory</h1>
            <p class="mt-1 text-sm text-gray-500">Record stock removals for damaged, expired, or manually adjusted items.</p>
        </div>

        {{-- Stock Out Form --}}
        <form action="{{ route('admin.inventory.stock-out.store') }}" method="POST" x-data="{
            products: {{ Js::from($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'stock' => $p->stock_quantity])) }},
            items: [{ product_id: '', quantity: 1, reason: '', notes: '' }],
            addItem() {
                this.items.push({ product_id: '', quantity: 1, reason: '', notes: '' });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            getAvailableStock(productId) {
                const p = this.products.find(p => p.id == productId);
                return p ? p.stock : 0;
            },
            get totalItems() {
                return this.items.filter(i => i.product_id !== '').length;
            },
            get totalQuantity() {
                return this.items.reduce((sum, i) => sum + (parseInt(i.quantity) || 0), 0);
            }
        }">
            @csrf

            {{-- Line Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Items to Remove</h2>

                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 space-y-3">
                            <div class="flex flex-col sm:flex-row gap-3">
                                {{-- Product Select --}}
                                <div class="flex-1 min-w-0">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Product</label>
                                    <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                                        <option value="">Select a product...</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} (SKU: {{ $product->sku ?? 'N/A' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Quantity --}}
                                <div class="w-full sm:w-36">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Quantity</label>
                                    <div class="relative">
                                        <input type="number" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" min="1"
                                               :max="getAvailableStock(item.product_id)" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400" x-show="item.product_id">
                                        Available: <span class="font-semibold" x-text="getAvailableStock(item.product_id)"></span>
                                    </p>
                                </div>
                                {{-- Remove Button --}}
                                <div class="flex items-start pt-5">
                                    <button type="button" @click="removeItem(index)" :disabled="items.length === 1"
                                            class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                {{-- Reason --}}
                                <div class="w-full sm:w-56">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Reason</label>
                                    <select :name="'items[' + index + '][reason]'" x-model="item.reason" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                                        <option value="">Select reason...</option>
                                        <option value="damaged">Damaged</option>
                                        <option value="expired">Expired</option>
                                        <option value="returned">Returned</option>
                                        <option value="manual_adjustment">Manual Adjustment</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                {{-- Notes --}}
                                <div class="flex-1 min-w-0">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                                    <input type="text" :name="'items[' + index + '][notes]'" x-model="item.notes" placeholder="Additional details..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Add Item Button --}}
                <button type="button" @click="addItem()" class="mt-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Another Item
                </button>
            </div>

            {{-- Summary & Submit --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex gap-6">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Items</p>
                            <p class="text-xl font-bold text-gray-900" x-text="totalItems">0</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Total Quantity</p>
                            <p class="text-xl font-bold text-red-600" x-text="'-' + totalQuantity">-0</p>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Record Stock Out
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection
