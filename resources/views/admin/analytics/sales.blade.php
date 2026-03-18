@extends('admin.layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('admin.analytics.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-amber-600 transition-colors mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Analytics
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Sales Report</h1>
        </div>
    </div>

    {{-- Date Range Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form action="{{ route('admin.analytics.sales') }}" method="GET" class="flex flex-wrap items-end gap-4">
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
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-50 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 10v1"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Total Sales</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($data['total_sales'] ?? $data['total_revenue'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Total Orders</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($data['total_orders'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Avg Order Value</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($data['avg_order_value'] ?? $data['average_order_value'] ?? 0, 2) }}</p>
        </div>
    </div>

    {{-- Daily Sales Chart --}}
    @if(isset($data['daily_sales']) && count($data['daily_sales']) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Daily Sales</h2>
        <canvas id="dailySalesChart" height="100"></canvas>
    </div>
    @endif

    {{-- Top Products Table --}}
    @if(isset($data['top_products']) && count($data['top_products']) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Top Products</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Units Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($data['top_products'] as $index => $product)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product['name'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($product['total_sold'] ?? 0) }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($product['revenue'] ?? 0, 2) }}</td>
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
            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900">No report data</h3>
        <p class="mt-1 text-sm text-gray-500">Select a date range and click "Generate Report" to view sales data.</p>
    </div>
    @endif
</div>

@if(isset($report) && isset($data['daily_sales']) && count($data['daily_sales']) > 0)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailySales = @json($data['daily_sales']);
    new Chart(document.getElementById('dailySalesChart'), {
        type: 'line',
        data: {
            labels: dailySales.map(d => d.date),
            datasets: [{
                label: 'Revenue',
                data: dailySales.map(d => d.total),
                borderColor: '#d97706',
                backgroundColor: 'rgba(217, 119, 6, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } }
            }
        }
    });
});
</script>
@endpush
@endif
@endsection
