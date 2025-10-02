@extends('admin.layouts.app')

@section('title', 'Modern KPI Dashboard')

@push('styles')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    body {
        font-family: 'Inter', sans-serif;
    }
    
    .glass-morphism {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    .metric-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .metric-card:hover {
        transform: translateY(-4px) scale(1.02);
    }
    
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .animated-counter {
        transition: all 0.8s ease;
    }
    
    @keyframes slideInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .slide-in-up {
        animation: slideInUp 0.6s ease-out;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen ">
    <div class="max-w-7xl ml-12 mr-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section -->
        <div class="glass-morphism rounded-3xl p-6 mb-8 slide-in-up text-gray-600">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold mb-2">
                        Analytics Dashboard
                    </h1>
                    <p>Real-time business insights and KPI tracking</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <!-- Time Period Selector -->
                    <select id="timeframeSelect" class="bg-white/20 backdrop-blur-md border border-gray/30  placeholder-gray/70 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray/50 transition-all">
                        <option value="today" {{ $timeframe === 'today' ? 'selected' : '' }} class="text-gray-900">Today</option>
                        <option value="yesterday" {{ $timeframe === 'yesterday' ? 'selected' : '' }} class="text-gray-900">Yesterday</option>
                        <option value="this_week" {{ $timeframe === 'this_week' ? 'selected' : '' }} class="text-gray-900">This Week</option>
                        <option value="last_week" {{ $timeframe === 'last_week' ? 'selected' : '' }} class="text-gray-900">Last Week</option>
                        <option value="this_month" {{ $timeframe === 'this_month' ? 'selected' : '' }} class="text-gray-900">This Month</option>
                        <option value="last_month" {{ $timeframe === 'last_month' ? 'selected' : '' }} class="text-gray-900">Last Month</option>
                        <option value="this_year" {{ $timeframe === 'this_year' ? 'selected' : '' }} class="text-gray-900">This Year</option>
                    </select>
                    
                    <!-- Export Button -->
                    <a href="{{ route('admin.kpi.export', ['timeframe' => $timeframe]) }}" 
                       class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="glass-morphism rounded-2xl p-6 metric-card slide-in-up border-l-4 border-amber-400">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-amber-500/20 rounded-xl">
                        <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium /80 bg-green-500/20 px-3 py-1 rounded-full">+12.5%</span>
                </div>
                <h3 class="/80 text-sm font-medium mb-2">Total Revenue</h3>
                <p class="text-3xl font-bold  animated-counter" data-target="{{ $kpis['sales']['total_revenue'] }}">
                    ${{ number_format($kpis['sales']['total_revenue'], 2) }}
                </p>
            </div>

            <!-- Average Order Value -->
            <div class="glass-morphism rounded-2xl p-6 metric-card slide-in-up border-l-4 border-blue-400" style="animation-delay: 0.1s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-500/20 rounded-xl">
                        <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium /80 bg-blue-500/20 px-3 py-1 rounded-full">+8.2%</span>
                </div>
                <h3 class="/80 text-sm font-medium mb-2">Average Order Value</h3>
                <p class="text-3xl font-bold  animated-counter">
                    ${{ number_format($kpis['sales']['average_order_value'], 2) }}
                </p>
            </div>

            <!-- Total Orders -->
            <div class="glass-morphism rounded-2xl p-6 metric-card slide-in-up border-l-4 border-purple-400" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-500/20 rounded-xl">
                        <svg class="w-8 h-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium /80 bg-purple-500/20 px-3 py-1 rounded-full">{{ $kpis['sales']['total_orders'] }}</span>
                </div>
                <h3 class="/80 text-sm font-medium mb-2">Total Orders</h3>
                <p class="text-3xl font-bold  animated-counter">
                    {{ number_format($kpis['sales']['total_orders']) }}
                </p>
                <div class="flex gap-4 text-sm mt-2">
                    <span class="text-green-300">✅ {{ $kpis['sales']['completed_orders'] }} completed</span>
                    <span class="text-red-300">❌ {{ $kpis['sales']['cancelled_orders'] }} cancelled</span>
                </div>
            </div>

            <!-- New Customers -->
            <div class="glass-morphism rounded-2xl p-6 metric-card slide-in-up border-l-4 border-yellow-400" style="animation-delay: 0.3s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-yellow-500/20 rounded-xl">
                        <svg class="w-8 h-8 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium /80 bg-yellow-500/20 px-3 py-1 rounded-full">+15.3%</span>
                </div>
                <h3 class="/80 text-sm font-medium mb-2">New Customers</h3>
                <p class="text-3xl font-bold  animated-counter">
                    {{ number_format($kpis['customers']['new_customers']) }}
                </p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Sales Chart -->
            <div class="glass-morphism rounded-2xl p-6 slide-in-up col-span-2" style="animation-delay: 0.4s;">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold ">Sales Trend</h3>
                    <div class="flex gap-2">
                        <button class="chart-period-btn bg-white/20 px-3 py-1 rounded-lg text-sm active" data-period="7days">7D</button>
                        <button class="chart-period-btn bg-white/10 px-3 py-1 rounded-lg text-sm" data-period="30days">30D</button>
                        <button class="chart-period-btn bg-white/10 px-3 py-1 rounded-lg text-sm" data-period="90days">90D</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart" class="h-[350px] w-full"></canvas>
                </div>
            </div>
            <!-- Top Selling Products -->
            <div class="glass-morphism rounded-2xl p-6 slide-in-up" style="animation-delay: 0.7s;">
                <h3 class="text-xl font-bold  mb-6 flex items-center gap-3">
                    <div class="p-2 bg-green-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    Top Selling Products
                </h3>
                <div>
                    @foreach($kpis['products']['top_selling'] as $index => $product)
                    <div class="flex items-center justify-between p-3 bg-white/10 rounded-xl hover:bg-white/15 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center  font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <span class=" font-medium truncate">{{ Str::limit($product->name, 30) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-bold ">{{ $product->total_sold ?? 0 }}</span>
                            <div class="text-xs /60">sold</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="glass-morphism rounded-2xl p-6 slide-in-up mb-8" style="animation-delay: 0.7s;">
            <h3 class="text-xl font-bold mb-6">Product Performance Metrics</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit Margin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($kpis['products']['performance'] as $product)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $product['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${{ number_format($product['revenue'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($product['units_sold']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="@if($product['profit_margin'] > 0) text-green-600 @else text-red-600 @endif">
                                    {{ number_format($product['profit_margin'], 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-amber-600 h-2.5 rounded-full" 
                                            style="width: {{ min(100, ($product['stock_quantity'] / $product['stock_threshold']) * 100) }}%">
                                        </div>
                                    </div>
                                    <span class="text-sm">{{ $product['stock_quantity'] }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Product Performance Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Orders Status Chart -->
            <div class="glass-morphism rounded-2xl p-6 slide-in-up" style="animation-delay: 0.5s;">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold ">Order Status Distribution</h3>
                </div>
                <div class="chart-container">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>

            <!-- Product Overview -->
            <div class="glass-morphism rounded-2xl p-6 slide-in-up" style="animation-delay: 0.6s;">
                <h3 class="text-xl font-bold  mb-6 flex items-center gap-3">
                    <div class="p-2 bg-indigo-500/20 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    Product Overview
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-white/10 rounded-xl">
                        <span class="/80 font-medium">Total Products</span>
                        <span class="text-2xl font-bold ">{{ $kpis['products']['total_products'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-orange-500/20 rounded-xl border border-orange-500/30">
                        <span class="font-medium">Low Stock Items</span>
                        <span class="text-2xl font-bold text-orange-400">{{ $kpis['products']['low_stock_products'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-red-500/20 rounded-xl border border-red-500/30">
                        <span class="font-medium">Out of Stock</span>
                        <span class="text-2xl font-bold text-red-600">{{ $kpis['products']['out_of_stock'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="glass-morphism rounded-2xl p-8 flex items-center gap-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
        <span class=" font-medium">Loading analytics...</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts
    initializeSalesChart();
    initializeOrderStatusChart();
    
    // Timeframe selector
    document.getElementById('timeframeSelect').addEventListener('change', function() {
        showLoading();
        window.location.href = "{{ route('admin.dashboard') }}?timeframe=" + this.value;
    });
    
    // Chart period buttons
    document.querySelectorAll('.chart-period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.chart-period-btn').forEach(b => {
                b.classList.remove('active', 'bg-white/20');
                b.classList.add('bg-white/10');
            });
            this.classList.add('active', 'bg-white/20');
            this.classList.remove('bg-white/10');
            
            updateSalesChart(this.dataset.period);
        });
    });
    
    // Animate counters
    animateCounters();
});

function showLoading() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('hidden');
}

function initializeSalesChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Sample data - replace with actual data from your controller
    const salesData = {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Sales',
            data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
            borderColor: 'rgba(59, 130, 246, 1)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgba(59, 130, 246, 1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8,
        }]
    };
    
    window.salesChart = new Chart(ctx, {
        type: 'line',
        data: salesData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        drawBorder: false,
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.8)',
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        drawBorder: false,
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.8)'
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: 'rgba(59, 130, 246, 1)',
                }
            }
        }
    });
}

function initializeOrderStatusChart() {
    const ctx = document.getElementById('orderStatusChart').getContext('2d');
    
    const orderStatusData = {
        labels: ['Completed', 'Processing', 'Shipped', 'Pending', 'Cancelled'],
        datasets: [{
            data: [{{ $kpis['sales']['completed_orders'] }}, 15, 8, 12, {{ $kpis['sales']['cancelled_orders'] }}],
            backgroundColor: [
                'rgba(16, 185, 129, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(156, 163, 175, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderColor: [
                'rgba(16, 185, 129, 1)',
                'rgba(59, 130, 246, 1)',
                'rgba(245, 158, 11, 1)',
                'rgba(156, 163, 175, 1)',
                'rgba(239, 68, 68, 1)'
            ],
            borderWidth: 2
        }]
    };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: orderStatusData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.8)',
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            cutout: '60%',
        }
    });
}

function updateSalesChart(period) {
    // Fetch new data based on period and update chart
    // This would typically make an AJAX call to your backend
    showLoading();
    
    fetch(`{{ route('admin.kpi.chart-data') }}?type=sales&period=${period}`)
        .then(response => response.json())
        .then(data => {
            window.salesChart.data.labels = data.labels;
            window.salesChart.data.datasets[0].data = data.datasets.data;
            window.salesChart.update();
            hideLoading();
        })
        .catch(error => {
            console.error('Error updating chart:', error);
            hideLoading();
        });
}

function animateCounters() {
    document.querySelectorAll('.animated-counter').forEach(counter => {
        const target = parseFloat(counter.dataset.target || counter.textContent.replace(/[^\d.-]/g, ''));
        const increment = target / 100;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            if (counter.textContent.includes('$')) {
                counter.textContent = '$' + current.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                counter.textContent = Math.floor(current).toLocaleString();
            }
        }, 20);
    });
}
</script>
@endpush