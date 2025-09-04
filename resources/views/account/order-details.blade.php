
@extends('layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('orders.index') }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium mb-2 inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Orders
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                <p class="text-gray-600 mt-1">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
                <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ number_format($order->total_amount, 2) }}</p>
            </div>
        </div>
    </div>
    <!-- Action Buttons -->
    @if($order->status === 'pending' || $order->status === 'processing')
        <div class="mb-6">
            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline-block" 
                onsubmit="return confirm('Are you sure you want to cancel this order?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Cancel Order
                </button>
            </form>
        </div>
    @endif
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">


        <!-- Order Tracking -->
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Order Tracking
                </h2>
            </div>
            <div class="p-6">
                <!-- Progress Timeline -->
                <div class="relative">
                    @php
                        $trackingSteps = [
                            ['status' => 'pending', 'title' => 'Order Placed', 'description' => 'Your order has been received and is being processed'],
                            ['status' => 'processing', 'title' => 'Order Confirmed', 'description' => 'Your order has been confirmed and is being prepared'],
                            ['status' => 'shipped', 'title' => 'Package Shipped', 'description' => 'Your package is on its way to you'],
                            ['status' => 'delivered', 'title' => 'Package Delivered', 'description' => 'Your package has been delivered successfully']
                        ];
                        
                        $currentStepIndex = array_search($order->status, array_column($trackingSteps, 'status'));
                        if ($currentStepIndex === false) $currentStepIndex = 0;
                    @endphp

                    <div class="space-y-0">
                        @foreach($trackingSteps as $index => $step)
                            <div class="relative flex items-start">
                                <!-- Timeline line -->
                                @if(!$loop->last)
                                    @php
                                        // Calculate dynamic height based on content
                                        $baseHeight = 80; // Base height for normal steps
                                        $extraHeight = 0;
                                        
                                        if ($step['status'] === 'shipped' && $index <= $currentStepIndex && isset($trackingData) && $trackingData && isset($trackingData['tracking_history'])) {
                                            $historyCount = count($trackingData['tracking_history']);
                                            $extraHeight = ($historyCount * 60) + 200; // Extra height for tracking history
                                        }
                                        
                                        $totalHeight = $baseHeight + $extraHeight;
                                    @endphp
                                    <div class="absolute left-4 top-8 w-0.5 {{ $index < $currentStepIndex ? 'bg-green-500' : 'bg-gray-200' }}" 
                                        style="height: {{ $totalHeight }}px;">
                                    </div>
                                @endif
                                
                                <!-- Step icon -->
                                <div class="relative flex items-center justify-center w-8 h-8 rounded-full border-2 z-10
                                    {{ $index <= $currentStepIndex ? 'bg-green-500 border-green-500' : 'bg-white border-gray-300' }}">
                                    @if($index <= $currentStepIndex)
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                    @endif
                                </div>
                                
                                <!-- Step content -->
                                <div class="ml-4 flex-1 pb-8">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium {{ $index <= $currentStepIndex ? 'text-gray-900' : 'text-gray-500' }}">
                                            {{ $step['title'] }}
                                        </h3>
                                        @if($index <= $currentStepIndex)
                                            <span class="text-xs text-gray-500">
                                                @if($index === 0) {{ $order->created_at->format('M d, Y g:i A') }}
                                                @elseif($index === 1 && isset($order->confirmed_at)) {{ $order->confirmed_at->format('M d, Y g:i A') }}
                                                @elseif($index === 2 && isset($order->shipped_at)) {{ $order->shipped_at->format('M d, Y g:i A') }}
                                                @elseif($index === 3 && isset($order->delivered_at)) {{ $order->delivered_at->format('M d, Y g:i A') }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs {{ $index <= $currentStepIndex ? 'text-gray-600' : 'text-gray-400' }} mt-1">
                                        {{ $step['description'] }}
                                    </p>
                                    
                                    <!-- Detailed Tracking Information for Shipped Status -->
                                    @if($step['status'] === 'shipped' && $index <= $currentStepIndex)
                                        <!-- Basic tracking info header -->
                                        @if($order->tracking_number)
                                            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-blue-800 font-medium">Tracking Number:</span>
                                                        <span class="text-blue-900 font-mono">{{ $order->tracking_number }}</span>
                                                    </div>
                                                    @if($order->shipping_method)
                                                        <div class="flex justify-between">
                                                            <span class="text-blue-800 font-medium">Courier:</span>
                                                            <span class="text-blue-900">{{ ucfirst($order->shipping_method) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                @if(isset($trackingData) && $trackingData)
                                                    <div class="mt-3 pt-3 border-t border-blue-200">
                                                        <div class="flex justify-between text-sm mb-2">
                                                            <span class="text-blue-800 font-medium">Current Status:</span>
                                                            <span class="text-blue-900 font-semibold">{{ $trackingData['status'] ?? 'In Transit' }}</span>
                                                        </div>
                                                        <div class="flex justify-between text-sm">
                                                            <span class="text-blue-800 font-medium">Est. Delivery:</span>
                                                            <span class="text-blue-900">{{ $trackingData['estimated_delivery'] ?? 'TBD' }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Detailed tracking history -->
                                        @if(isset($trackingData) && $trackingData && isset($trackingData['tracking_history']) && count($trackingData['tracking_history']) > 0)
                                            <div class="mt-6">
                                                <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                    </svg>
                                                    Package Journey
                                                </h4>
                                                
                                                <!-- Tracking timeline -->
                                                <div class="relative">
                                                    <!-- Vertical line for tracking history -->
                                                    <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-blue-200"></div>
                                                    
                                                    <div class="space-y-6">
                                                        @foreach($trackingData['tracking_history'] as $trackingIndex => $tracking)
                                                            <div class="relative flex items-start">
                                                                <!-- Timeline dot -->
                                                                <div class="relative z-10 flex items-center justify-center w-6 h-6 
                                                                    {{ $trackingIndex === 0 ? 'bg-blue-600' : 'bg-blue-400' }} 
                                                                    rounded-full border-2 border-white shadow-sm">
                                                                    @if($trackingIndex === 0)
                                                                        <!-- Current/Latest status icon -->
                                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                        </svg>
                                                                    @else
                                                                        <!-- Previous status dots -->
                                                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                                                    @endif
                                                                </div>
                                                                
                                                                <!-- Tracking details -->
                                                                <div class="ml-4 flex-1">
                                                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                                                        <h5 class="text-sm font-medium text-gray-900">{{ $tracking['location'] }}</h5>
                                                                        <span class="text-xs text-gray-500 mt-1 sm:mt-0">
                                                                            {{ \Carbon\Carbon::parse($tracking['date'])->format('M d, Y g:i A') }}
                                                                        </span>
                                                                    </div>
                                                                    <p class="text-xs text-gray-600 mt-1">{{ $tracking['status'] }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a1 1 0 001 1h12a1 1 0 001-1V8m-1 0v-1a1 1 0 011-1h2v-2H3v2h2v1z"/>
                        </svg>
                        Order Items
                    </h2>
                </div>
                <div class="p-6">
                    @if($order->items->count() > 0)
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <div class="w-16 h-16 bg-gray-200 rounded-md overflow-hidden">
                                            @if($item->product && $item->product->images->count() > 0)
                                                <img src="{{ asset('storage/' . $item->product->images->first()->image_url) }}" alt="{{ $item->product->name ?? 'Product' }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="font-medium text-gray-900">{{ $item->product_name ?? ($item->product->name ?? 'Product not found') }}</h3>
                                            <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">₱{{ number_format($item->unit_price, 2) }}</p>
                                        <p class="text-sm text-gray-500">Total: ₱{{ number_format($item->total_price, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No items found</h3>
                            <p class="mt-1 text-sm text-gray-500">This order doesn't have any items.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Customer Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Name</p>
                            <p class="text-sm text-gray-900">{{ $order->customer->fullname ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-sm text-gray-900">{{ $order->customer->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Phone</p>
                            <p class="text-sm text-gray-900">{{ $order->customer->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Shipping Address
                    </h2>
                </div>
                <div class="p-6">
                    @if($order->shippingAddress)
                        <address class="text-sm text-gray-600 not-italic">
                            <p>{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</p>
                            @if($order->shippingAddress->company)
                                <p>{{ $order->shippingAddress->company }}</p>
                            @endif
                            <p>{{ $order->shippingAddress->address_line_1 }}</p>
                            @if($order->shippingAddress->address_line_2)
                                <p>{{ $order->shippingAddress->address_line_2 }}</p>
                            @endif
                            <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                            <p>{{ $order->shippingAddress->country }}</p>
                        </address>
                    @else
                        <p class="text-sm text-gray-500">No shipping address available</p>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Payment Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Payment Method</p>
                            <p class="text-sm text-gray-900">{{ ucfirst($order->payment_method) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="text-sm text-gray-900">{{ ucfirst($order->payment_status) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Transaction ID</p>
                            <p class="text-sm text-gray-900">{{ $order->transaction_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function trackPackage(trackingNumber, provider) {
    // Show loading state
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Tracking...';
    button.disabled = true;
    
    fetch('{{ route("orders.track-package") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            tracking_number: trackingNumber,
            provider: provider
        })
    })
    .then(response => response.json())
    .then(data => {
        // Create and show tracking modal
        showTrackingModal(data);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to fetch tracking information. Please try again.');
    })
    .finally(() => {
        // Reset button
        button.textContent = originalText;
        button.disabled = false;
    });
}

function showTrackingModal(trackingData) {
    // Create modal HTML
    const modalHTML = `
        <div id="trackingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Package Tracking</h3>
                        <button onclick="closeTrackingModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-blue-800">Tracking Number:</span>
                                <span class="text-blue-900 font-mono">${trackingData.tracking_number}</span>
                            </div>
                            <div>
                                <span class="font-medium text-blue-800">Provider:</span>
                                <span class="text-blue-900">${trackingData.provider.toUpperCase()}</span>
                            </div>
                            <div>
                                <span class="font-medium text-blue-800">Status:</span>
                                <span class="text-blue-900">${trackingData.status}</span>
                            </div>
                            <div>
                                <span class="font-medium text-blue-800">Est. Delivery:</span>
                                <span class="text-blue-900">${trackingData.estimated_delivery}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Tracking History</h4>
                        ${trackingData.tracking_history.map(item => `
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-900">${item.status}</span>
                                        <span class="text-sm text-gray-500">${new Date(item.date).toLocaleDateString()}</span>
                                    </div>
                                    <p class="text-sm text-gray-600">${item.description}</p>
                                    <p class="text-xs text-gray-500">${item.location}</p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeTrackingModal() {
    const modal = document.getElementById('trackingModal');
    if (modal) {
        modal.remove();
    }
}
</script>
@endpush

@endsection