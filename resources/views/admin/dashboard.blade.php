@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
    
    .coffee-gradient {
        background: linear-gradient(135deg, #8B4513 0%, #D2691E 50%, #CD853F 100%);
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .metric-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    }
    
    .metric-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    
    .coffee-bean-bg {
        background-image: 
            radial-gradient(circle at 20% 80%, rgba(139, 69, 19, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(210, 105, 30, 0.05) 0%, transparent 50%);
    }
    
    .pulse-dot {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
    
    .activity-timeline {
        position: relative;
    }
    
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 24px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #8B4513, #D2691E);
    }
    
    .chart-container {
        position: relative;
        height: 350px;
    }
    
    .coffee-icon-bg {
        background: linear-gradient(45deg, #8B4513, #D2691E);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen coffee-bean-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 -mt-4">
        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Sales -->
            <div class="metric-card rounded-2xl shadow-lg border border-gray-100 p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 coffee-gradient opacity-10 rounded-full -mr-10 -mt-10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-xl">
                            <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                        </div>
                        <div class="pulse-dot w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 mb-2">☕ Total Bean Sales</h3>
                    <p class="text-3xl font-bold text-gray-900 mb-3">${{ number_format($metrics['total_sales'] ?? 0, 2) }}</p>
                    <div class="flex items-center">
                        @if(($growthMetrics['sales_growth'] ?? 0) >= 0)
                            <i class="fas fa-arrow-up text-green-500 text-sm mr-2"></i>
                            <span class="text-green-600 text-sm font-semibold">{{ abs($growthMetrics['sales_growth'] ?? 0) }}%</span>
                        @else
                            <i class="fas fa-arrow-down text-red-500 text-sm mr-2"></i>
                            <span class="text-red-600 text-sm font-semibold">{{ abs($growthMetrics['sales_growth'] ?? 0) }}%</span>
                        @endif
                        <span class="text-gray-500 text-sm ml-1">vs last month</span>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="metric-card rounded-2xl shadow-lg border border-gray-100 p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 coffee-gradient opacity-10 rounded-full -mr-10 -mt-10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-xl">
                            <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                        </div>
                        <div class="pulse-dot w-3 h-3 bg-blue-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 mb-2">📦 Coffee Orders</h3>
                    <p class="text-3xl font-bold text-gray-900 mb-3">{{ number_format($metrics['total_orders'] ?? 0) }}</p>
                    <div class="flex items-center">
                        @if(($growthMetrics['orders_growth'] ?? 0) >= 0)
                            <i class="fas fa-arrow-up text-green-500 text-sm mr-2"></i>
                            <span class="text-green-600 text-sm font-semibold">{{ abs($growthMetrics['orders_growth'] ?? 0) }}%</span>
                        @else
                            <i class="fas fa-arrow-down text-red-500 text-sm mr-2"></i>
                            <span class="text-red-600 text-sm font-semibold">{{ abs($growthMetrics['orders_growth'] ?? 0) }}%</span>
                        @endif
                        <span class="text-gray-500 text-sm ml-1">vs last month</span>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="metric-card rounded-2xl shadow-lg border border-gray-100 p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 coffee-gradient opacity-10 rounded-full -mr-10 -mt-10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-100 p-3 rounded-xl">
                            <i class="fas fa-users text-purple-600 text-xl"></i>
                        </div>
                        <div class="pulse-dot w-3 h-3 bg-purple-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 mb-2">👥 Coffee Lovers</h3>
                    <p class="text-3xl font-bold text-gray-900 mb-3">{{ number_format($metrics['total_customers'] ?? 0) }}</p>
                    <div class="flex items-center">
                        <i class="fas fa-arrow-up text-green-500 text-sm mr-2"></i>
                        <span class="text-green-600 text-sm font-semibold">12.5%</span>
                        <span class="text-gray-500 text-sm ml-1">vs last month</span>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="metric-card rounded-2xl shadow-lg border border-gray-100 p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 coffee-gradient opacity-10 rounded-full -mr-10 -mt-10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-orange-100 p-3 rounded-xl">
                            <i class="fas fa-seedling text-orange-600 text-xl"></i>
                        </div>
                        <div class="pulse-dot w-3 h-3 bg-orange-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 mb-2">🫘 Bean Varieties</h3>
                    <p class="text-3xl font-bold text-gray-900 mb-3">{{ number_format($metrics['total_products'] ?? 0) }}</p>
                    <div class="flex items-center">
                        @if(($metrics['low_stock_products'] ?? 0) > 0)
                            <i class="fas fa-exclamation-triangle text-orange-500 text-sm mr-2"></i>
                            <span class="text-orange-600 text-sm font-semibold">{{ $metrics['low_stock_products'] ?? 0 }} low stock</span>
                        @else
                            <i class="fas fa-check-circle text-green-500 text-sm mr-2"></i>
                            <span class="text-green-600 text-sm font-semibold">All stocked</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Sales Chart -->
            <div class="lg:col-span-2 glass-card rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="coffee-icon-bg p-2 rounded-lg">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">☕ Bean Sales Overview</h3>
                    </div>
                    <div class="relative">
                        <select id="salesPeriod" class="bg-white border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent shadow-sm" onchange="updateChart('sales', this.value)">
                            <option value="7days">Last 7 Days</option>
                            <option value="30days">Last 30 Days</option>
                            <option value="90days">Last 90 Days</option>
                        </select>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Order Status Chart -->
            <div class="glass-card rounded-2xl shadow-xl p-6">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="coffee-icon-bg p-2 rounded-lg">
                        <i class="fas fa-chart-pie text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">📊 Order Status</h3>
                </div>
                <div class="chart-container">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quick Actions -->
            <div class="glass-card rounded-2xl shadow-xl p-6">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="coffee-icon-bg p-2 rounded-lg">
                        <i class="fas fa-bolt text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">⚡ Quick Actions</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.products.create') }}" class="group bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-4 rounded-xl text-sm font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg flex flex-col items-center space-y-2">
                        <i class="fas fa-plus-circle text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Add Bean</span>
                    </a>
                    
                    <a href="{{ route('admin.orders.index') }}" class="group bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-4 rounded-xl text-sm font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg flex flex-col items-center space-y-2">
                        <i class="fas fa-shopping-bag text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Orders</span>
                    </a>
                    
                    <a href="{{ route('admin.customers.index') }}" class="group bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-4 py-4 rounded-xl text-sm font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg flex flex-col items-center space-y-2">
                        <i class="fas fa-users text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Customers</span>
                    </a>
                    
                    <a href="{{ route('admin.analytics.index') }}" class="group bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-4 py-4 rounded-xl text-sm font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg flex flex-col items-center space-y-2">
                        <i class="fas fa-chart-bar text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Analytics</span>
                    </a>
                    
                    <a href="{{ route('admin.coupons.create') }}" class="group bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-4 rounded-xl text-sm font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg flex flex-col items-center space-y-2 col-span-2">
                        <i class="fas fa-ticket-alt text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Create Coffee Coupon</span>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="glass-card rounded-2xl shadow-xl p-6">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="coffee-icon-bg p-2 rounded-lg">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">🕐 Recent Activity</h3>
                </div>
                <div class="activity-timeline max-h-80 overflow-y-auto space-y-4">
                    @forelse($recentActivity as $activity)
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @if($activity['type'] === 'order')
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-500">
                                        📦
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-500">
                                        👤
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $activity['title'] }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $activity['description'] }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $activity['time']->diffForHumans() }}
                                </p>
                            </div>
                            @if(isset($activity['amount']))
                                <div class="flex-shrink-0">
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($activity['amount'], 2) }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No recent activity</p>
                    @endforelse
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="glass-card rounded-2xl shadow-xl p-6">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="coffee-icon-bg p-2 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">⚠️ Low Bean Stock</h3>
                </div>
                <div class="space-y-4 max-h-80 overflow-y-auto">
                    @forelse($lowStockProducts as $product)
                        <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-sm">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $product->name }}</h4>
                                <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold {{ $product->stock_quantity <= 5 ? 'text-red-600' : 'text-orange-500' }}">
                                    {{ $product->stock_quantity }} left
                                </p>
                                <p class="text-xs text-gray-500">Min: {{ $product->low_stock_threshold }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No products with low stock</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            <!-- Top Selling Products -->
            <div class="glass-card rounded-2xl shadow-xl p-6">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="coffee-icon-bg p-2 rounded-lg">
                        <i class="fas fa-trophy text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">🏆 Best Selling Beans</h3>
                </div>
                <div class="overflow-x-auto">
                  <table class="w-full">
                      <thead>
                          <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              <th class="px-4 py-3">Product</th>
                              <th class="px-4 py-3">Total Sold</th>
                              <th class="px-4 py-3">Stock</th>
                          </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-200">
                          @forelse($topSellingProducts as $product)
                              <tr>
                                  <td class="px-4 py-3">
                                      <div>
                                          <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                          <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                                      </div>
                                  </td>
                                  <td class="px-4 py-3 text-sm">{{ $product->total_sold ?? 0 }} units</td>
                                  <td class="px-4 py-3">
                                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                          {{ $product->stock_quantity > $product->low_stock_threshold ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                          {{ $product->stock_quantity }}
                                      </span>
                                  </td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="3" class="px-4 py-3 text-sm text-gray-500">No sales data available</td>
                              </tr>
                          @endforelse
                      </tbody>
                  </table>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="glass-card rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="coffee-icon-bg p-2 rounded-lg">
                            <i class="fas fa-receipt text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">📋 Recent Orders</h3>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="bg-orange-100 hover:bg-orange-200 text-orange-700 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:scale-105">
                        View All
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider pb-4">Order #</th>
                                <th class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider pb-4">Customer</th>
                                <th class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider pb-4">Amount</th>
                                <th class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider pb-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $recentOrders = \App\Models\Order::with('customer')->latest()->take(5)->get();
                            @endphp
                            @if($recentOrders->count() > 0)
                                @foreach($recentOrders as $order)
                                  <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="py-4 text-sm text-gray-900">{{ $order->customer->name ?? 'Guest' }}</td>
                                    <td class="py-4 text-sm font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</td>
                                    <td class="py-4">
                                      <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                          {{ ucfirst($order->status) }}
                                      </span>
                                    </td>
                                  </tr>
                               @endforeach
                           @else
                               <tr>
                                   <td colspan="4" class="py-8 text-center">
                                       <div class="coffee-icon-bg p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                           <i class="fas fa-shopping-cart text-white text-xl"></i>
                                       </div>
                                       <p class="text-gray-500 text-sm">No orders yet</p>
                                       <p class="text-gray-400 text-xs mt-1">Time to brew some business! ☕</p>
                                   </td>
                               </tr>
                           @endif
                       </tbody>
                   </table>
               </div>
           </div>
       </div>
   </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
  let salesChart; 
  let orderChart;
  // Coffee-themed color palette
  const coffeeColors = {
      primary: '#8B4513',
      secondary: '#D2691E',
      accent: '#CD853F',
      success: '#10b981',
      warning: '#f59e0b',
      danger: '#ef4444'
  };

  document.addEventListener('DOMContentLoaded', function() {
      initializeSalesChart();
      initializeOrderStatusChart();
  });

  // Initialize charts when DOM is loaded
  function initializeSalesChart() {
    fetch('/admin/chart-data/sales?period=7days')
      .then(response => response.json())
      .then(data => {
          const ctx = document.getElementById('salesChart').getContext('2d');
          new Chart(ctx, {
              type: 'line',
              data: {
                  labels: data.labels,
                  datasets: [{
                      label: 'Sales ($)',
                      data: data.datasets[0].data,
                      borderColor: '#D97706',
                      backgroundColor: 'rgba(217, 119, 6, 0.1)',
                      tension: 0.4,
                      fill: true
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                      legend: {
                          position: 'bottom',
                          labels: {
                              usePointStyle: true,
                              pointStyle: 'circle',
                              padding: 15,
                              font: {
                                  size: 11,
                                  weight: '500'
                              },
                              color: '#374151'
                          }
                      },
                      tooltip: {
                          backgroundColor: 'rgba(0, 0, 0, 0.8)',
                          titleColor: '#ffffff',
                          bodyColor: '#ffffff',
                          borderColor: coffeeColors.primary,
                          borderWidth: 1,
                          cornerRadius: 12,
                          callbacks: {
                              label: function(context) {
                                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                  const percentage = ((context.parsed / total) * 100).toFixed(1);
                                  return context.label + ': ' + context.parsed + ' orders (' + percentage + '%)';
                              }
                          }
                      }
                  },
                  cutout: '65%',
                  elements: {
                      arc: {
                          borderWidth: 0
                      }
                  }
              }
            });
    }).catch(error => {
          console.error('Error loading order status chart:', error);
    });
  }

  function initializeOrderStatusChart() {
    fetch('/admin/order-status-data')
      .then(response => response.json())
      .then(data => {
          const ctx = document.getElementById('orderStatusChart').getContext('2d');
          new Chart(ctx, {
              type: 'doughnut',
              data: {
                  labels: data.labels,
                  datasets: [{
                      data: data.data,
                      backgroundColor: [
                          '#10B981', // completed
                          '#F59E0B', // processing
                          '#3B82F6', // shipped
                          '#6B7280', // pending
                          '#EF4444'  // cancelled
                      ]
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                      legend: {
                          display: false
                      },
                      tooltip: {
                          backgroundColor: 'rgba(0, 0, 0, 0.8)',
                          titleColor: '#ffffff',
                          bodyColor: '#ffffff',
                          borderColor: coffeeColors.primary,
                          borderWidth: 1,
                          cornerRadius: 12,
                          displayColors: false,
                          callbacks: {
                              title: function(context) {
                                  return 'Coffee Sales - ' + context[0].label;
                              },
                              label: function(context) {
                                  return '$' + context.parsed.y.toLocaleString() + ' in bean sales ☕';
                              }
                          }
                      }
                  },
                  scales: {
                      y: {
                          beginAtZero: true,
                          grid: {
                              color: 'rgba(139, 69, 19, 0.1)',
                              drawBorder: false
                          },
                          ticks: {
                              color: '#6b7280',
                              font: {
                                  size: 11,
                                  weight: '500'
                              },
                              callback: function(value) {
                                  return '$' + value.toLocaleString();
                              }
                          }
                      },
                      x: {
                          grid: {
                              display: false
                          },
                          ticks: {
                              color: '#6b7280',
                              font: {
                                  size: 11,
                                  weight: '500'
                              }
                          }
                      }
                  },
                  interaction: {
                      intersect: false,
                      mode: 'index'
                  }
              }
          });
    }).catch(error => {
      console.error('Error loading sales chart:', error);
    });
  };

  function loadSalesChart(period = '7days') {
    // Add loading state
    if (salesChart) {
        salesChart.data.datasets[0].data = [];
        salesChart.update('active');
    }

    fetch(`/admin/chart-data/sales?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (salesChart) {
                salesChart.data.labels = data.labels;
                salesChart.data.datasets[0].data = data.datasets[0].data;
                salesChart.update('active');
            }
        })
        .catch(error => {
            console.error('Error loading sales chart:', error);
            // Use fallback data
            const fallbackData = {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                data: [1200, 1900, 3000, 5000, 2000, 3500, 4500]
            };
            if (salesChart) {
                salesChart.data.labels = fallbackData.labels;
                salesChart.data.datasets[0].data = fallbackData.data;
                salesChart.update('active');
            }
        });
  }

  function loadOrderStatusChart() {
    // Add loading state
    if (orderChart) {
        orderChart.data.datasets[0].data = [0, 0, 0, 0, 0];
        orderChart.update('active');
    }

    fetch('/admin/order-status-data')
        .then(response => response.json())
        .then(data => {
            if (orderChart) {
                orderChart.data.labels = data.labels;
                orderChart.data.datasets[0].data = data.data;
                orderChart.update('active');
            }
        })
        .catch(error => {
            console.error('Error loading order status chart:', error);
            // Use fallback data
            if (orderChart) {
                orderChart.data.datasets[0].data = [65, 15, 12, 5, 3];
                orderChart.update('active');
            }
        });
  }

  function updateChart(type, period) {
    if (type === 'sales') {
        loadSalesChart(period);
    }
  }

   // Add entrance animations
   window.addEventListener('load', function() {
       const elements = document.querySelectorAll('.glass-card, .metric-card');
       elements.forEach((element, index) => {
           element.style.opacity = '0';
           element.style.transform = 'translateY(30px)';
           
           setTimeout(() => {
               element.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
               element.style.opacity = '1';
               element.style.transform = 'translateY(0)';
           }, index * 150);
       });
   });

   // Add coffee bean floating animation
   function createFloatingBean() {
       const bean = document.createElement('div');
       bean.innerHTML = '☕';
       bean.style.position = 'fixed';
       bean.style.left = Math.random() * window.innerWidth + 'px';
       bean.style.top = window.innerHeight + 'px';
       bean.style.fontSize = Math.random() * 20 + 15 + 'px';
       bean.style.opacity = '0.1';
       bean.style.pointerEvents = 'none';
       bean.style.zIndex = '1';
       bean.style.transition = 'all 10s linear';
       
       document.body.appendChild(bean);
       
       setTimeout(() => {
           bean.style.top = '-50px';
           bean.style.transform = 'rotate(360deg)';
       }, 100);
       
       setTimeout(() => {
           document.body.removeChild(bean);
       }, 10000);
   }

   // Create floating coffee beans occasionally
   setInterval(createFloatingBean, 8000);
</script>
@endsection