@extends('layouts.app')

@section('title', 'My Account Dashboard')

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

            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-amber-600 to-amber-700 rounded-xl p-6 mb-8 text-white">
                <h1 class="text-2xl font-bold">Welcome back, {{ $customer->first_name }}! ☕</h1>
                <p class="mt-1 text-amber-100">Manage your account and view your order history.</p>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center">
                        <div class="p-2.5 rounded-lg bg-blue-50">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center">
                        <div class="p-2.5 rounded-lg bg-green-50">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Spent</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($totalSpent, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center">
                        <div class="p-2.5 rounded-lg bg-amber-50">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Member Since</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $customer->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                    <a href="{{ route('orders.index') }}" class="text-sm font-medium text-amber-600 hover:text-amber-700">
                        View All →
                    </a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">Order #{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('M j, Y') }} · ${{ number_format($order->total_amount, 2) }}</p>
                            </div>
                            <div class="flex items-center gap-3 ml-4">
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
                                <a href="{{ route('orders.show', $order) }}" class="text-amber-600 hover:text-amber-700 text-sm font-medium">
                                    View
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 px-6">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <h3 class="mt-3 text-sm font-medium text-gray-900">No orders yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Start shopping to see your orders here.</p>
                            <div class="mt-4">
                                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    Start Shopping
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
