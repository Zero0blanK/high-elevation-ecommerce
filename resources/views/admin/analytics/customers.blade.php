@extends('admin.layouts.app')

@section('title', 'Customer Report')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('admin.analytics.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-amber-600 transition-colors mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Analytics
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Customer Report</h1>
        </div>
    </div>

    {{-- Date Range Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form action="{{ route('admin.analytics.customers') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="period_start" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="period_start" id="period_start"
                       value="{{ request('period_start', now()->subMonth()->format('Y-m-d')) }}"
                       class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm" required>
            </div>
            <div>
                <label for="period_end" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="period_end" id="period_end"
                       value="{{ request('period_end', now()->format('Y-m-d')) }}"
                       class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm" required>
            </div>
            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">Generate Report</button>
        </form>
    </div>

    @if(isset($report))
    @php
        $data = is_array($report) ? ($report['data'] ?? $report) : ($report->data ?? []);
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Total Customers</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($data['total_customers'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-50 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">New Customers</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($data['new_customers'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Returning Customers</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($data['returning_customers'] ?? 0) }}</p>
        </div>
    </div>

    {{-- Top Customers Table --}}
    @if(isset($data['top_customers']) && count($data['top_customers']) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Top Customers</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Spent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($data['top_customers'] as $index => $customer)
                    @php $custName = trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')) ?: 'N/A'; @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">
                                    {{ strtoupper(substr($custName, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $custName }}</span>
                                    @if(!empty($customer['email']))
                                        <p class="text-xs text-gray-500">{{ $customer['email'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($customer['order_count'] ?? 0) }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($customer['total_spent'] ?? 0, 2) }}</td>
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
            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900">No report data</h3>
        <p class="mt-1 text-sm text-gray-500">Select a date range and click "Generate Report" to view customer data.</p>
    </div>
    @endif
</div>
@endsection
