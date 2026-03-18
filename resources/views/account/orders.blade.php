@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <!-- Sidebar (desktop) -->
        <div class="hidden lg:block lg:col-span-3">
            @include('account.partials.sidebar')
        </div>

        <div class="lg:col-span-9">
            <!-- Mobile tab navigation -->
            <div class="lg:hidden mb-6 flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <a href="{{ route('account.dashboard') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('account.dashboard') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Dashboard</a>
                <a href="{{ route('orders.index') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('orders.*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Orders</a>
                <a href="{{ route('wishlist.index') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('wishlist.*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Wishlist</a>
                <a href="{{ route('account.addresses') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('account.addresses*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Addresses</a>
                <a href="{{ route('account.profile') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium {{ request()->routeIs('account.profile*') ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">Profile</a>
            </div>

            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
                <p class="text-sm text-gray-500 mt-1">Track and manage your orders</p>
            </div>

            <!-- Status Filter Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                        <a href="{{ route('orders.index') }}"
                           class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium {{ request('status') == '' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            All <span class="ml-1.5 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-700">{{ $orderCounts['all'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('orders.index', ['status' => 'pending']) }}"
                           class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium {{ request('status') == 'pending' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Pending <span class="ml-1.5 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-700">{{ $orderCounts['pending'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('orders.index', ['status' => 'processing']) }}"
                           class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium {{ request('status') == 'processing' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Processing <span class="ml-1.5 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-700">{{ $orderCounts['processing'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('orders.index', ['status' => 'shipped']) }}"
                           class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium {{ request('status') == 'shipped' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Shipped <span class="ml-1.5 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-700">{{ $orderCounts['shipped'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('orders.index', ['status' => 'delivered']) }}"
                           class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium {{ request('status') == 'delivered' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Delivered <span class="ml-1.5 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-700">{{ $orderCounts['delivered'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('orders.index', ['status' => 'cancelled']) }}"
                           class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium {{ request('status') == 'cancelled' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Cancelled <span class="ml-1.5 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-700">{{ $orderCounts['cancelled'] ?? 0 }}</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Search & Sort -->
            <div class="mb-6 flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <form method="GET" action="{{ route('orders.index') }}" class="flex gap-2">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by order number..."
                               class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Search
                        </button>
                    </form>
                </div>
                <select onchange="window.location.href=this.value" class="px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="{{ route('orders.index', array_merge(request()->all(), ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Newest First</option>
                    <option value="{{ route('orders.index', array_merge(request()->all(), ['sort' => 'oldest'])) }}" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="{{ route('orders.index', array_merge(request()->all(), ['sort' => 'amount_high'])) }}" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Amount: High to Low</option>
                    <option value="{{ route('orders.index', array_merge(request()->all(), ['sort' => 'amount_low'])) }}" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Amount: Low to High</option>
                </select>
            </div>

            <!-- Orders List -->
            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Order Header -->
                            <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-sm font-semibold text-gray-900">Order #{{ $order->order_number }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-sm">
                                        <span class="text-gray-500">{{ $order->created_at->format('M d, Y') }}</span>
                                        <span class="font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items Preview -->
                            <div class="p-5">
                                @foreach($order->items->take(3) as $item)
                                    <div class="flex items-center gap-4 {{ !$loop->last ? 'mb-3 pb-3 border-b border-gray-100' : '' }}">
                                        <div class="flex-shrink-0 w-14 h-14 rounded-lg overflow-hidden bg-gray-100">
                                            <img src="{{ $item->product->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</h4>
                                            <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}
                                                @if($item->product_variant) · {{ $item->product_variant }} @endif
                                            </p>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">${{ number_format(($item->product->is_on_sale ? $item->product->sale_price : $item->product->price) * $item->quantity, 2) }}</span>
                                    </div>
                                @endforeach

                                @if($order->items->count() > 3)
                                    <p class="mt-3 text-xs text-gray-500">+ {{ $order->items->count() - 3 }} more item(s)</p>
                                @endif
                            </div>

                            <!-- Order Footer -->
                            <div class="bg-gray-50 px-5 py-3 border-t border-gray-200 flex items-center justify-between">
                                <span class="text-xs text-gray-500">{{ $order->items->sum('quantity') }} item(s)</span>
                                <div class="flex items-center gap-2">
                                    @if($order->status === 'shipped' && $order->tracking_number)
                                        <button onclick="trackOrder('{{ $order->tracking_number }}', '{{ $order->shipping_provider }}')"
                                                class="text-xs font-medium text-amber-600 hover:text-amber-700 px-3 py-1.5 border border-amber-300 rounded-lg hover:bg-amber-50 transition-colors">
                                            Track Package
                                        </button>
                                    @endif
                                    <a href="{{ route('orders.show', $order) }}"
                                       class="text-xs font-medium text-white bg-amber-600 hover:bg-amber-700 px-3 py-1.5 rounded-lg transition-colors">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $orders->appends(request()->except('page'))->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-3 text-sm font-medium text-gray-900">No orders found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by placing your first order.</p>
                    <div class="mt-4">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Browse Products
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function trackOrder(trackingNumber, provider) {
    alert(`Tracking ${trackingNumber} via ${provider}\nIn a real application, this would open a tracking modal or redirect to the carrier's tracking page.`);
}
</script>
@endsection
