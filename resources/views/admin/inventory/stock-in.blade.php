@extends('admin.layouts.app')

@section('title', 'Stock In — Receive Inventory')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Page Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.inventory.index') }}" class="inline-flex items-center text-sm font-medium text-amber-600 hover:text-amber-700 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Inventory
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Stock In — Receive Inventory</h1>
            <p class="mt-1 text-sm text-gray-500">Record incoming stock from purchases, deliveries, or transfers.</p>
        </div>

        {{-- Stock In Form --}}
        <form action="{{ route('admin.inventory.stock-in.store') }}" method="POST" x-data="{
            items: [{ product_id: '', quantity: 1, notes: '' }],
            addItem() {
                this.items.push({ product_id: '', quantity: 1, notes: '' });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            get totalItems() {
                return this.items.filter(i => i.product_id !== '').length;
            },
            get totalQuantity() {
                return this.items.reduce((sum, i) => sum + (parseInt(i.quantity) || 0), 0);
            }
        }">
            @csrf

            {{-- Reference Number --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Reference Number <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" placeholder="PO number, delivery reference, etc."
                       class="w-full sm:w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
            </div>

            {{-- Line Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Items to Receive</h2>

                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex flex-col sm:flex-row gap-3 p-4 bg-gray-50 rounded-lg border border-gray-100">
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
                            <div class="w-full sm:w-28">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Quantity</label>
                                <input type="number" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" min="1" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                            </div>
                            {{-- Notes --}}
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
                                <input type="text" :name="'items[' + index + '][notes]'" x-model="item.notes" placeholder="Batch number, condition, etc."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                            </div>
                            {{-- Remove Button --}}
                            <div class="flex items-end">
                                <button type="button" @click="removeItem(index)" :disabled="items.length === 1"
                                        class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
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
                            <p class="text-xl font-bold text-green-600" x-text="'+' + totalQuantity">+0</p>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Record Stock In
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection
