@extends('layouts.app')

@section('title', 'Order Confirmation')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12 mt-16 text-center">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Order Confirmed!</h1>
        <p class="text-gray-600 mb-6">Thank you for your order. We'll send you an email confirmation shortly.</p>

        @if(isset($order))
            <div class="bg-gray-50 rounded-lg p-6 text-left mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Order Number</span>
                        <p class="font-semibold text-gray-900">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Total</span>
                        <p class="font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Payment Status</span>
                        <p class="font-semibold capitalize text-gray-900">{{ $order->payment_status }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Date</span>
                        <p class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('orders.index') }}" class="bg-amber-600 text-white px-6 py-3 rounded-lg hover:bg-amber-700 transition font-semibold">
                View My Orders
            </a>
            <a href="{{ route('products.index') }}" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
