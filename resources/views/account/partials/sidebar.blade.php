@php
    $customer = auth('customer')->user();
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Customer Info -->
    <div class="p-5 bg-gradient-to-br from-amber-50 to-orange-50 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="h-12 w-12 rounded-full bg-amber-600 flex items-center justify-center flex-shrink-0">
                <span class="text-lg font-bold text-white">{{ substr($customer->first_name, 0, 1) }}{{ substr($customer->last_name, 0, 1) }}</span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ $customer->email }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="p-3 space-y-1">
        <a href="{{ route('account.dashboard') }}"
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('account.dashboard') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('account.dashboard') ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('orders.index') }}"
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('orders.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('orders.*') ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            My Orders
        </a>

        <a href="{{ route('wishlist.index') }}"
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('wishlist.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('wishlist.*') ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            Wishlist
        </a>

        <a href="{{ route('account.addresses') }}"
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('account.addresses*') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('account.addresses*') ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Addresses
        </a>

        <a href="{{ route('account.profile') }}"
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('account.profile*') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('account.profile*') ? 'text-amber-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profile
        </a>
    </nav>

    <!-- Logout -->
    <div class="p-3 border-t border-gray-200">
        <form method="POST" action="{{ route('customer.logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-red-50 hover:text-red-700 transition-colors">
                <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>
