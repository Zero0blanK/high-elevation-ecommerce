@extends('admin.layouts.app')

@section('title', 'Analytics & Reports')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Analytics & Reports</h1>
        <p class="mt-1 text-sm text-gray-500">Monitor your business performance and generate reports.</p>
    </div>

    {{-- Report Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Sales Report --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
            <div class="p-6">
                <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-amber-50 text-amber-600 mb-4 group-hover:bg-amber-100 transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Sales Report</h3>
                <p class="text-sm text-gray-500 mb-5">Revenue trends, top products, and daily sales breakdowns.</p>
                <a href="{{ route('admin.analytics.sales') }}" class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                    View Report
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Customer Report --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
            <div class="p-6">
                <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-blue-50 text-blue-600 mb-4 group-hover:bg-blue-100 transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Customer Report</h3>
                <p class="text-sm text-gray-500 mb-5">Customer acquisition, retention, and lifetime value analysis.</p>
                <a href="{{ route('admin.analytics.customers') }}" class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                    View Report
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Inventory Report --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
            <div class="p-6">
                <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-green-50 text-green-600 mb-4 group-hover:bg-green-100 transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Inventory Report</h3>
                <p class="text-sm text-gray-500 mb-5">Stock levels, product values, and out-of-stock monitoring.</p>
                <a href="{{ route('admin.analytics.inventory') }}" class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                    View Report
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Export Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Export Reports</h2>
                    <p class="text-sm text-gray-500">Generate and download reports in your preferred format.</p>
                </div>
            </div>
        </div>
        <form action="{{ route('admin.analytics.export') }}" method="POST" class="px-6 py-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                    <select name="type" id="type" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                        <option value="sales">Sales</option>
                        <option value="customers">Customers</option>
                        <option value="inventory">Inventory</option>
                    </select>
                </div>
                <div>
                    <label for="format" class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                    <select name="format" id="format" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <div>
                    <label for="export_period_start" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="period_start" id="export_period_start" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                </div>
                <div>
                    <label for="export_period_end" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="period_end" id="export_period_end" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                </div>
                <div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
