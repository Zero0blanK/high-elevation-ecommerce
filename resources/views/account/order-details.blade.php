@extends('layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

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
                <a href="{{ route('account.dashboard') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">Dashboard</a>
                <a href="{{ route('orders.index') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-amber-600 text-white">Orders</a>
                <a href="{{ route('wishlist.index') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">Wishlist</a>
                <a href="{{ route('account.addresses') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">Addresses</a>
                <a href="{{ route('account.profile') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">Profile</a>
            </div>

            <!-- Back link + Header -->
            <div class="mb-6">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center text-sm font-medium text-amber-600 hover:text-amber-700 mb-3">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Orders
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'processing') bg-blue-100
                            @elseif($order->status === 'shipped') bg-purple-100
                            @elseif($order->status === 'delivered') bg-green-100
                            @elseif($order->status === 'cancelled') bg-red-100
                            @else bg-gray-100
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                        @if($order->status === 'pending' || $order->status === 'processing')
                            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 px-3 py-1 border border-red-300 rounded-lg hover:bg-red-50 transition-colors">
                                    Cancel Order
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Tracking -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-900 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Order Tracking
                    </h2>
                </div>
                <div class="p-5">
                    @php
                        $trackingSteps = [
                            ['status' => 'pending', 'title' => 'Order Placed', 'description' => 'Your order has been received'],
                            ['status' => 'processing', 'title' => 'Order Confirmed', 'description' => 'Your order is being prepared'],
                            ['status' => 'shipped', 'title' => 'Package Shipped', 'description' => 'Your package is on its way'],
                            ['status' => 'delivered', 'title' => 'Delivered', 'description' => 'Package delivered successfully']
                        ];
                        $currentStepIndex = array_search($order->status, array_column($trackingSteps, 'status'));
                        if ($currentStepIndex === false) $currentStepIndex = 0;
                    @endphp

                    <div class="flex items-center justify-between relative">
                        <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-200"></div>
                        <div class="absolute top-4 left-0 h-0.5 bg-green-500" style="width: {{ $currentStepIndex > 0 ? ($currentStepIndex / (count($trackingSteps) - 1)) * 100 : 0 }}%"></div>

                        @foreach($trackingSteps as $index => $step)
                            <div class="relative flex flex-col items-center flex-1">
                                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center z-10
                                    {{ $index <= $currentStepIndex ? 'bg-green-500 border-green-500' : 'bg-white border-gray-300' }}">
                                    @if($index <= $currentStepIndex)
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @else
                                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs font-medium text-center {{ $index <= $currentStepIndex ? 'text-gray-900' : 'text-gray-400' }}">{{ $step['title'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if($order->tracking_number && $order->status === 'shipped')
                        <div class="mt-5 p-3 bg-blue-50 rounded-lg border border-blue-200 text-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-blue-800 font-medium">Tracking:</span>
                                    <span class="text-blue-900 font-mono ml-1">{{ $order->tracking_number }}</span>
                                </div>
                                @if($order->shipping_method)
                                    <span class="text-blue-700">{{ ucfirst($order->shipping_method) }}</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-900">Order Items ({{ $order->items->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($order->items as $item)
                        <div class="p-5 flex items-center gap-4">
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                @if($item->product && $item->product->primaryImage)
                                    <img src="{{ $item->product->primaryImage->image_url }}" alt="{{ $item->product->name ?? 'Product' }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900">{{ $item->product_name ?? ($item->product->name ?? 'Product not found') }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Qty: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}</p>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">${{ number_format($item->total_price, 2) }}</p>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-gray-500">No items found</div>
                    @endforelse
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 px-5 py-4 border-t border-gray-200">
                    <div class="space-y-2 max-w-xs ml-auto">
                        @if(isset($order->subtotal))
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span>${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                        @endif
                        @if(isset($order->tax_amount) && $order->tax_amount > 0)
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Tax</span>
                                <span>${{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                        @endif
                        @if(isset($order->shipping_amount))
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Shipping</span>
                                <span>${{ number_format($order->shipping_amount, 2) }}</span>
                            </div>
                        @endif
                        @if(isset($order->discount_amount) && $order->discount_amount > 0)
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Discount</span>
                                <span>-${{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span>${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping & Payment Info -->
            <div class="grid sm:grid-cols-2 gap-6">
                <!-- Shipping Address -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Shipping Address
                        </h2>
                    </div>
                    <div class="p-5">
                        @if($order->shippingAddress)
                            <address class="text-sm text-gray-600 not-italic space-y-0.5">
                                <p class="font-medium text-gray-900">{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</p>
                                @if($order->shippingAddress->company)<p>{{ $order->shippingAddress->company }}</p>@endif
                                <p>{{ $order->shippingAddress->address_line_1 }}</p>
                                @if($order->shippingAddress->address_line_2)<p>{{ $order->shippingAddress->address_line_2 }}</p>@endif
                                <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                                <p>{{ $order->shippingAddress->country }}</p>
                            </address>
                        @else
                            <p class="text-sm text-gray-400">No shipping address available</p>
                        @endif
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-sm font-semibold text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Payment Information
                        </h2>
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Method</p>
                            <p class="text-sm text-gray-900 mt-0.5">{{ ucfirst($order->payment_method) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</p>
                            <p class="text-sm text-gray-900 mt-0.5">{{ ucfirst($order->payment_status) }}</p>
                        </div>
                        @if($order->transaction_id)
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Transaction ID</p>
                                <p class="text-sm text-gray-900 mt-0.5 font-mono">{{ $order->transaction_id }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function trackPackage(trackingNumber, provider) {
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Tracking...';
    button.disabled = true;

    fetch('{{ route("orders.track-package") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ tracking_number: trackingNumber, provider: provider })
    })
    .then(response => response.json())
    .then(data => showTrackingModal(data))
    .catch(() => alert('Failed to fetch tracking information.'))
    .finally(() => { button.textContent = originalText; button.disabled = false; });
}

function showTrackingModal(trackingData) {
    const modalHTML = `
        <div id="trackingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-xl bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Package Tracking</h3>
                    <button onclick="document.getElementById('trackingModal').remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="mb-4 p-4 bg-blue-50 rounded-lg text-sm">
                    <div class="grid grid-cols-2 gap-3">
                        <div><span class="font-medium text-blue-800">Tracking:</span> <span class="text-blue-900 font-mono">${trackingData.tracking_number}</span></div>
                        <div><span class="font-medium text-blue-800">Provider:</span> <span class="text-blue-900">${trackingData.provider.toUpperCase()}</span></div>
                        <div><span class="font-medium text-blue-800">Status:</span> <span class="text-blue-900">${trackingData.status}</span></div>
                        <div><span class="font-medium text-blue-800">Est. Delivery:</span> <span class="text-blue-900">${trackingData.estimated_delivery}</span></div>
                    </div>
                </div>
                <div class="space-y-3">
                    <h4 class="font-medium text-gray-900 text-sm">Tracking History</h4>
                    ${trackingData.tracking_history.map(item => `
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900 text-sm">${item.status}</span>
                                    <span class="text-xs text-gray-500">${new Date(item.date).toLocaleDateString()}</span>
                                </div>
                                <p class="text-xs text-gray-600">${item.description}</p>
                                <p class="text-xs text-gray-500">${item.location}</p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}
</script>
@endpush

@endsection