@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' - High Elevation Coffee')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                    <p class="text-gray-600">
                        Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
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
                    <a href="{{ route('orders.index') }}" 
                       class="text-gray-600 hover:text-gray-900 flex items-center">
                        <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Orders
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-6">
                            @foreach($order->items as $item)
                                <div class="flex items-start space-x-4">
                                    @if($item->product && $item->product->image_url)
                                        <img src="{{ $item->product->image_url }}" 
                                             alt="{{ $item->product->name }}"
                                             class="w-16 h-16 object-cover rounded-md">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $item->product ? $item->product->name : 'Product Unavailable' }}
                                        </h3>
                                        @if($item->product && $item->product->description)
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ Str::limit($item->product->description, 100) }}
                                            </p>
                                        @endif
                                        <div class="mt-2 flex items-center justify-between">
                                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                <span>Quantity: {{ $item->quantity }}</span>
                                                <span>Price: ${{ number_format($item->price, 2) }}</span>
                                            </div>
                                            <div class="text-lg font-semibold text-gray-900">
                                                ${{ number_format($item->quantity * $item->price, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Timeline</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Order placed</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $order->created_at->format('M j, Y g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                        @if(!in_array($order->status, ['pending']))
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                    </div>
                                </li>

                                @if(!in_array($order->status, ['pending', 'cancelled']))
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Order confirmed and processing</p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        {{ $order->updated_at->format('M j, Y g:i A') }}
                                                    </div>
                                                </div>
                                            </div>
                                            @if(!in_array($order->status, ['processing']))
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                        </div>
                                    </li>
                                @endif

                                @if(in_array($order->status, ['shipped', 'delivered']))
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Order shipped</p>
                                                        @if($order->tracking_number)
                                                            <p class="text-xs text-gray-400">Tracking: {{ $order->tracking_number }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        {{ $order->shipped_at ? $order->shipped_at->format('M j, Y g:i A') : 'Pending' }}
                                                    </div>
                                                </div>
                                            </div>
                                            @if($order->status === 'delivered')
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                    </li>
                                @endif

                                @if($order->status === 'delivered')
                                    <li>
                                        <div class="relative">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h0a2 2 0 012 2v0H8v0z"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Order delivered</p>
                                                        <p class="text-xs text-gray-400">Your order has been successfully delivered</p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        {{ $order->delivered_at ? $order->delivered_at->format('M j, Y g:i A') : 'Recently' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif

                                @if($order->status === 'cancelled')
                                    <li>
                                        <div class="relative">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Order cancelled</p>
                                                        @if($order->cancellation_reason)
                                                            <p class="text-xs text-gray-400">Reason: {{ $order->cancellation_reason }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        {{ $order->cancelled_at ? $order->cancelled_at->format('M j, Y g:i A') : 'Recently' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            
                            @if($order->tax_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax</span>
                                    <span class="text-gray-900">${{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($order->shipping_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="text-gray-900">${{ number_format($order->shipping_amount, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Discount</span>
                                    <span class="text-red-600">-${{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-base font-medium text-gray-900">Total</span>
                                    <span class="text-base font-medium text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Shipping Information</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Delivery Address</h3>
                                <div class="mt-1 text-sm text-gray-600">
                                    <p>{{ $order->shipping_name }}</p>
                                    <p>{{ $order->shipping_address_line_1 }}</p>
                                    @if($order->shipping_address_line_2)
                                        <p>{{ $order->shipping_address_line_2 }}</p>
                                    @endif
                                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                                    <p>{{ $order->shipping_country }}</p>
                                </div>
                            </div>
                            
                            @if($order->estimated_delivery_date)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Estimated Delivery</h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ $order->estimated_delivery_date->format('F j, Y') }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($order->tracking_number)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Tracking Number</h3>
                                    <p class="mt-1 text-sm text-gray-600 font-mono">
                                        {{ $order->tracking_number }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Payment Information</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Payment Method</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ ucfirst($order->payment_method) }}
                                    @if($order->payment_method === 'card' && $order->card_last_four)
                                        ending in {{ $order->card_last_four }}
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Payment Status</h3>
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                            
                            @if($order->transaction_id)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Transaction ID</h3>
                                    <p class="mt-1 text-sm text-gray-600 font-mono">
                                        {{ $order->transaction_id }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-3">
                            @if($order->status === 'delivered')
                                <form action="{{ route('orders.reorder', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Reorder Items
                                    </button>
                                </form>
                                
                                <a href="{{ route('products.reviews.create', ['order' => $order->id]) }}" 
                                   class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 inline-flex items-center justify-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                    Leave Review
                                </a>
                            @endif
                            
                            @if(in_array($order->status, ['pending', 'processing']))
                                <form action="{{ route('orders.cancel', $order) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                                        <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Cancel Order
                                    </button>
                                </form>
                            @endif
                            
                            @if($order->status === 'shipped' && $order->tracking_number)
                                <a href="{{ route('orders.track', $order) }}" 
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 inline-flex items-center justify-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Track Package
                                </a>
                            @endif
                            
                            <!-- Download Invoice -->
                            <a href="{{ route('orders.invoice', $order) }}" 
                               class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 inline-flex items-center justify-center">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download Invoice
                            </a>
                            
                            <!-- Contact Support -->
                            <a href="{{ route('support.contact', ['order' => $order->order_number]) }}" 
                               class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 inline-flex items-center justify-center">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Contact Support
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Order Notes (if any) -->
                @if($order->notes)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Order Notes</h2>
                        </div>
                        <div class="px-6 py-6">
                            <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif
                <!-- Payment Details -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Payment Details</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Payment Method</span>
                                <div class="flex items-center">
                                    @if($order->payment_method === 'card')
                                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900">
                                            •••• •••• •••• {{ $order->card_last_four ?? '****' }}
                                        </span>
                                    @elseif($order->payment_method === 'paypal')
                                        <span class="text-sm text-gray-900">PayPal</span>
                                    @else
                                        <span class="text-sm text-gray-900">{{ ucfirst($order->payment_method) }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Payment Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                    @elseif($order->payment_status === 'refunded') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                            
                            @if($order->transaction_id)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Transaction ID</span>
                                    <span class="text-sm text-gray-900 font-mono">{{ $order->transaction_id }}</span>
                                </div>
                            @endif
                            @if($order->payment_date)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Payment Date</h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ $order->payment_date->format('F j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($order->billing_address_line_1)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Billing Address</h3>
                                    <div class="mt-1 text-sm text-gray-600">
                                        <p>{{ $order->billing_name ?? $order->shipping_name }}</p>
                                        <p>{{ $order->billing_address_line_1 }}</p>
                                        @if($order->billing_address_line_2)
                                            <p>{{ $order->billing_address_line_2 }}</p>
                                        @endif
                                        <p>{{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}</p>
                                        <p>{{ $order->billing_country }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional Information Section -->
        @if($order->special_instructions || $order->gift_message)
            <div class="mt-8">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Additional Information</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-4">
                            @if($order->special_instructions)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Special Instructions</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $order->special_instructions }}</p>
                                </div>
                            @endif
                            
                            @if($order->gift_message)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Gift Message</h3>
                                    <div class="mt-1 p-3 bg-amber-50 border border-amber-200 rounded-md">
                                        <p class="text-sm text-amber-800">{{ $order->gift_message }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Related Orders Section -->
        @if($relatedOrders && $relatedOrders->count() > 0)
            <div class="mt-8">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Your Recent Orders</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-4">
                            @foreach($relatedOrders as $relatedOrder)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center space-x-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                Order #{{ $relatedOrder->order_number }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $relatedOrder->created_at->format('M j, Y') }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($relatedOrder->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($relatedOrder->status === 'processing') bg-blue-100 text-blue-800
                                            @elseif($relatedOrder->status === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($relatedOrder->status === 'delivered') bg-green-100 text-green-800
                                            @elseif($relatedOrder->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($relatedOrder->status) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm font-medium text-gray-900">
                                            ${{ number_format($relatedOrder->total_amount, 2) }}
                                        </span>
                                        <a href="{{ route('orders.show', $relatedOrder) }}" 
                                           class="text-amber-600 hover:text-amber-700 text-sm font-medium">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
         x-data="{ show: true }" 
         x-show="show" 
         x-transition
         x-init="setTimeout(() => show = false, 5000)">
        <div class="flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
         x-data="{ show: true }" 
         x-show="show" 
         x-transition
         x-init="setTimeout(() => show = false, 5000)">
        <div class="flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    </div>
@endif

@push('scripts')
<script>
    // Auto-refresh tracking information if order is shipped
    @if($order->status === 'shipped' && $order->tracking_number)
        setInterval(function() {
            // Check for tracking updates every 5 minutes
            fetch('{{ route("orders.tracking-status", $order) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'delivered') {
                        location.reload();
                    }
                })
                .catch(error => console.log('Tracking check failed:', error));
        }, 300000); // 5 minutes
    @endif
    
    // Print functionality
    function printOrder() {
        window.print();
    }
    
    // Copy order number to clipboard
    function copyOrderNumber() {
        navigator.clipboard.writeText('{{ $order->order_number }}').then(function() {
            // Show success message
            const message = document.createElement('div');
            message.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            message.textContent = 'Order number copied to clipboard!';
            document.body.appendChild(message);
            
            setTimeout(() => {
                document.body.removeChild(message);
            }, 3000);
        });
    }
</script>
@endpush

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        .print-only {
            display: block !important;
        }
        
        body {
            font-size: 12px;
        }
        
        .shadow {
            box-shadow: none !important;
        }
        
        .bg-white {
            background: white !important;
        }
    }
</style>
@endpush
@endsection
                