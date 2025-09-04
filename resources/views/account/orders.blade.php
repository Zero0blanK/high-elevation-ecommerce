
@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
        <p class="text-gray-600 mt-2">Track and manage your orders</p>
    </div>

    <!-- Order Status Filter Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('orders.index') }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm
                   {{ request('status') == '' ? 'border-amber-500 text-amber-600' : '' }}">
                    All Orders
                    <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $orderCounts['all'] ?? 0 }}</span>
                </a>
                <a href="{{ route('orders.index', ['status' => 'pending']) }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm
                   {{ request('status') == 'pending' ? 'border-amber-500 text-amber-600' : '' }}">
                    To Pay
                    <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $orderCounts['pending'] ?? 0 }}</span>
                </a>
                <a href="{{ route('orders.index', ['status' => 'processing']) }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm
                   {{ request('status') == 'processing' ? 'border-amber-500 text-amber-600' : '' }}">
                    To Ship
                    <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $orderCounts['processing'] ?? 0 }}</span>
                </a>
                <a href="{{ route('orders.index', ['status' => 'shipped']) }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm
                   {{ request('status') == 'shipped' ? 'border-amber-500 text-amber-600' : '' }}">
                    To Receive
                    <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $orderCounts['shipped'] ?? 0 }}</span>
                </a>
                <a href="{{ route('orders.index', ['status' => 'delivered']) }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm
                   {{ request('status') == 'delivered' ? 'border-amber-500 text-amber-600' : '' }}">
                    Completed
                    <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $orderCounts['delivered'] ?? 0 }}</span>
                </a>
                <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm
                   {{ request('status') == 'cancelled' ? 'border-amber-500 text-amber-600' : '' }}">
                    Cancelled
                    <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $orderCounts['cancelled'] ?? 0 }}</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <form method="GET" action="{{ route('orders.index') }}" class="flex gap-2">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search by order number or product name..." 
                       class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md font-medium">
                    Search
                </button>
            </form>
        </div>
        <div class="flex gap-2">
            <select onchange="window.location.href=this.value" class="border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                <option value="{{ route('orders.index', array_merge(request()->all(), ['sort' => 'newest'])) }}" 
                        {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>
                    Newest First
                </option>
                <option value="{{ route('orders.index', array_merge(request()->all(), ['sort' => 'oldest'])) }}" 
                        {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                    Oldest First
                </option>
                <option value="{{ route('orders.index', array_merge(request()->all(), ['sort' => 'amount_high'])) }}" 
                        {{ request('sort') == 'amount_high' ? 'selected' : '' }}>
                    Amount: High to Low
                </option>
                <option value="{{ route('orders.index', array_merge(request()->all(), ['sort' => 'amount_low'])) }}" 
                        {{ request('sort') == 'amount_low' ? 'selected' : '' }}>
                    Amount: Low to High
                </option>
            </select>
        </div>
    </div>

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                    <!-- Order Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Order #{{ $order->order_number }}</h3>
                                    <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                                <div class="flex items-center">
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
                            </div>
                            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                                <span class="text-lg font-bold text-gray-900">₱{{ number_format($order->total_amount, 2) }}</span>
                                @if($order->status === 'shipped' && $order->tracking_number)
                                    <button onclick="trackOrder('{{ $order->tracking_number }}', '{{ $order->shipping_provider }}')" 
                                            class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded text-sm font-medium">
                                        Track Package
                                    </button>
                                @endif
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm font-medium">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="p-6">
                        @foreach($order->items->take(3) as $item)
                            <div class="flex items-center space-x-4 {{ !$loop->last ? 'mb-4 pb-4 border-b border-gray-100' : '' }}">
                                <div class="flex-shrink-0">
                                    <img src="{{ $item->product->image_url ?? '/images/placeholder.jpg' }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="h-16 w-16 rounded-lg object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</h4>
                                    <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                    @if($item->product_variant)
                                        <p class="text-xs text-gray-400">{{ $item->product_variant }}</p>
                                    @endif
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    ₱${{ number_format(($item->product->is_on_sale ? $item->product->sale_price : $item->product->price) * $item->quantity, 2) }}
                                </div>
                            </div>
                        @endforeach

                        @if($order->items->count() > 3)
                            <div class="mt-4 text-center">
                                <a href="{{ route('orders.show', $order) }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">
                                    View {{ $order->items->count() - 3 }} more items
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->appends(request()->except('page'))->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by placing an order.</p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                    Browse Products
                </a>
            </div>
        </div>
    @endif
</div>

<script>
function trackOrder(trackingNumber, provider) {
    // Simple tracking implementation - you can expand this with actual API calls
    alert(`Tracking ${trackingNumber} via ${provider}\nIn a real application, this would open a tracking modal or redirect to the carrier's tracking page.`);
}
</script>
@endsection
