@extends('layouts.app')

@section('title', 'Track Your Order - High Elevation Coffee')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12 mt-16">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Track Your Order</h1>

    <div class="bg-white rounded-lg shadow-md p-8">
        <form action="{{ route('orders.track.submit') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label for="order_number" class="block text-sm font-medium text-gray-700 mb-2">Order Number</label>
                <input type="text" name="order_number" id="order_number" placeholder="e.g. ORD-ABC123"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-lg"
                       value="{{ old('order_number') }}" required>
                @error('order_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" id="email" placeholder="your@email.com"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-lg"
                       value="{{ old('email') }}" required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-amber-600 text-white py-3 px-6 rounded-lg hover:bg-amber-700 transition font-semibold">
                Track Order
            </button>
        </form>

        @if(isset($order))
            <div class="mt-8 border-t pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order #{{ $order->order_number }}</h2>

                {{-- Status Timeline --}}
                <div class="relative">
                    @php
                        $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                        $currentIndex = array_search($order->status, $statuses);
                        if ($order->status === 'cancelled') $currentIndex = -1;
                    @endphp

                    <div class="flex justify-between mb-2">
                        @foreach($statuses as $index => $status)
                            <div class="flex flex-col items-center flex-1">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                    {{ $index <= $currentIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                                    @if($index <= $currentIndex)
                                        ✓
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <span class="mt-2 text-xs font-medium {{ $index <= $currentIndex ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($order->status === 'cancelled')
                        <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                            This order has been cancelled.
                        </div>
                    @endif
                </div>

                {{-- Order Details --}}
                <div class="mt-6 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Status</span>
                        <span class="font-semibold capitalize {{ $order->status === 'cancelled' ? 'text-red-600' : 'text-gray-900' }}">{{ $order->status }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total</span>
                        <span class="font-semibold">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    @if($order->tracking_number)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tracking Number</span>
                            <span class="font-semibold text-amber-600">{{ $order->tracking_number }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Placed</span>
                        <span class="font-semibold">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
