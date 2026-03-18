@extends('admin.layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('admin.analytics.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-amber-600 transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Analytics
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Inventory Report</h1>
        <p class="mt-1 text-sm text-gray-500">Current snapshot of your product inventory.</p>
    </div>

    @if(isset($report))
    @php
        $data = is_array($report) ? ($report['data'] ?? $report) : ($report->data ?? []);
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Total Products</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($data['total_products'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-50 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Total Stock</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($data['total_stock'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Low Stock Items</p>
            </div>
            <p class="text-3xl font-bold text-amber-600">
                @php $lowCount = isset($data['low_stock_products']) && is_countable($data['low_stock_products']) ? count($data['low_stock_products']) : ($data['low_stock_products'] ?? 0); @endphp
                {{ $lowCount }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-red-50 text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Out of Stock</p>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $data['out_of_stock_count'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Category Breakdown --}}
    @if(isset($data['category_breakdown']) && count($data['category_breakdown']) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Category Breakdown</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Products</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($data['category_breakdown'] as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $category['name'] ?? 'Uncategorized' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($category['product_count'] ?? 0) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($category['total_stock'] ?? 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Low Stock Items --}}
    @php
        $lowStockItems = $data['low_stock_products'] ?? [];
    @endphp
    @if(is_countable($lowStockItems) && count($lowStockItems) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h2 class="text-lg font-semibold text-gray-900">Low Stock Items</h2>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($lowStockItems as $product)
                    @php
                        $p = is_array($product) ? $product : (array) $product;
                        $stock = $p['stock_quantity'] ?? 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $p['name'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $p['sku'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm font-semibold {{ $stock == 0 ? 'text-red-600' : 'text-amber-600' }}">{{ $stock }}</td>
                        <td class="px-6 py-4">
                            @if($stock == 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Out of Stock
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Low Stock
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @else
    {{-- Empty State --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mx-auto mb-4">
            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900">Unable to load report</h3>
        <p class="mt-1 text-sm text-gray-500">There was an issue generating the inventory report. Please try again.</p>
    </div>
    @endif
</div>
@endsection
