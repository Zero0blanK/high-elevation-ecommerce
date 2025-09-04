
@extends('layouts.app')

@section('title', 'Order Confirmation - ' . config('ecommerce.store.name'))
@section('description', 'Your order has been successfully placed.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
    <div class="text-center mb-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
        <p class="text-lg text-gray-600">Thank you for your purchase. Your order has been successfully placed.</p>
    </div>

    @if(isset($order) && $order)
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <!-- Order Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Order #{{ $order->order_number }}</h2>
                    <p class="text-sm text-gray-600 mt-1">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Order Items -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                    <div class="space-y-4">
                        @if($order->items && $order->items->count() > 0)
                            @foreach($order->items as $item)
                            <div class="flex items-center space-x-4">
                                <img src="{{ $item->product->image ?? '/images/placeholder.jpg' }}" alt="{{ $item->product->name ?? 'Product' }}" 
                                     class="w-16 h-16 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $item->product->name ?? 'Product Name' }}</h4>
                                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                    <p class="text-sm text-gray-600">Price: ${{ number_format($item->product->is_on_sale ? $item->product->sale_price : $item->product->price, 2)}} each</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900">${{ number_format(($item->product->is_on_sale ? $item->product->sale_price : $item->product->price) * $item->quantity, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-gray-600">No items found for this order.</p>
                        @endif
                    </div>
                </div>

                <!-- Order Summary -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">${{ number_format($order->subtotal ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium">${{ number_format($order->shipping_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-medium">${{ number_format($order->tax_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg font-semibold">
                                <span>Total</span>
                                <span>${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Addresses -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Shipping Address -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        @if($order->shippingAddress)
                            <p class="font-medium text-gray-900">{{ $order->shippingAddress->full_name }}</p>
                            @if($order->shippingAddress->company)
                                <p class="text-gray-600">{{ $order->shippingAddress->company }}</p>
                            @endif
                            <p class="text-gray-600">{{ $order->shippingAddress->address_line_1 }}</p>
                            @if($order->shippingAddress->address_line_2)
                                <p class="text-gray-600">{{ $order->shippingAddress->address_line_2 }}</p>
                            @endif
                            <p class="text-gray-600">
                                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}
                            </p>
                            <p class="text-gray-600">{{ $order->shippingAddress->country }}</p>
                        @else
                            <p class="text-gray-600">Shipping address not available</p>
                        @endif
                    </div>
                </div>

                <!-- Billing Address -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Billing Address</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        @if($order->billingAddress)
                            <p class="font-medium text-gray-900">{{ $order->billingAddress->full_name }}</p>
                            @if($order->billingAddress->company)
                                <p class="text-gray-600">{{ $order->billingAddress->company }}</p>
                            @endif
                            <p class="text-gray-600">{{ $order->billingAddress->address_line_1 }}</p>
                            @if($order->billingAddress->address_line_2)
                                <p class="text-gray-600">{{ $order->billingAddress->address_line_2 }}</p>
                            @endif
                            <p class="text-gray-600">
                                {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}
                            </p>
                            <p class="text-gray-600">{{ $order->billingAddress->country }}</p>
                        @else
                            <p class="text-gray-600">Billing address not available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-600">
                        <span class="font-medium">Payment Method:</span> 
                        {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}
                    </p>
                    <p class="text-gray-600 mt-1">
                        <span class="font-medium">Payment Status:</span> 
                        <span class="capitalize">{{ $order->payment_status ?? 'pending' }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
        @if(Route::has('orders.show'))
            <a href="{{ route('orders.show', $order->order_number) }}" 
               class="bg-amber-600 text-white px-6 py-3 rounded-lg hover:bg-amber-700 transition-colors font-medium text-center">
                View Order Details
            </a>
        @endif
        <a href="{{ route('products.index') }}" 
           class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium text-center">
            Continue Shopping
        </a>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8 text-center">
        <p class="text-gray-600 mb-4">Order details could not be found.</p>
        <a href="{{ route('products.index') }}" 
           class="bg-amber-600 text-white px-6 py-3 rounded-lg hover:bg-amber-700 transition-colors font-medium">
            Continue Shopping
        </a>
    </div>
    @endif

    <!-- Email Confirmation Notice -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="h-5 w-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="text-sm font-medium text-blue-900">Order Confirmation Email</h4>
                <p class="text-sm text-blue-700 mt-1">
                    A confirmation email with your order details has been sent to your email address. 
                    If you don't receive it within a few minutes, please check your spam folder.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
