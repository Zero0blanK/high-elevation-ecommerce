<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'High Elevation Coffee')</title>
    <meta name="description" content="@yield('description', 'Premium coffee beans delivered fresh to your door')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <style>[x-cloak] { display: none !important; }</style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('meta')
</head>
<body class="bg-gray-50 font-[Inter,Figtree,sans-serif]">

    <!-- Navigation -->
    <nav class="bg-white/95 backdrop-blur-sm shadow-sm border-b border-gray-200 fixed top-0 w-full left-0 z-50"
         x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center group">
                        <img class="h-8 w-auto" src="/images/logo.png" alt="High Elevation Coffee">
                        <span class="ml-2 text-xl font-bold text-gray-900 group-hover:text-amber-600 transition-colors">High Elevation</span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-700 hover:text-amber-600 transition-colors {{ request()->routeIs('home') ? 'text-amber-600' : '' }}">Home</a>
                    <a href="{{ route('products.index') }}" class="text-sm font-medium text-gray-700 hover:text-amber-600 transition-colors {{ request()->routeIs('products.*') ? 'text-amber-600' : '' }}">Shop</a>
                    <a href="{{ route('about') }}" class="text-sm font-medium text-gray-700 hover:text-amber-600 transition-colors {{ request()->routeIs('about') ? 'text-amber-600' : '' }}">About</a>
                    <a href="{{ route('contact') }}" class="text-sm font-medium text-gray-700 hover:text-amber-600 transition-colors {{ request()->routeIs('contact') ? 'text-amber-600' : '' }}">Contact</a>
                </div>

                <!-- Right side items -->
                <div class="flex items-center space-x-3">
                    <!-- Search -->
                    <div class="hidden md:block">
                        <form action="{{ route('products.index') }}" method="GET" class="relative">
                            <input type="text"
                                   name="search"
                                   placeholder="Search coffee..."
                                   value="{{ request('search') }}"
                                   class="w-56 lg:w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-gray-50">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </form>
                    </div>

                    <!-- User Menu -->
                    @auth('customer')
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-amber-600 transition-colors">
                                <div class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-amber-700">{{ substr(Auth::guard('customer')->user()->first_name, 0, 1) }}</span>
                                </div>
                                <span class="ml-2 text-sm font-medium hidden lg:inline">{{ Auth::guard('customer')->user()->first_name }}</span>
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 py-1 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::guard('customer')->user()->first_name }} {{ Auth::guard('customer')->user()->last_name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::guard('customer')->user()->email }}</p>
                                </div>
                                <a href="{{ route('account.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    My Orders
                                </a>
                                <a href="{{ route('wishlist.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    Wishlist
                                </a>
                                <a href="{{ route('account.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profile
                                </a>
                                <a href="{{ route('account.addresses') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700">
                                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    Addresses
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('customer.logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700">
                                        <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <button onclick="openLoginModal()" class="text-sm font-medium text-gray-700 hover:text-amber-600 transition-colors hidden sm:inline-flex">
                            Sign In
                        </button>
                        <button onclick="openRegisterModal()" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-full text-sm font-medium transition-colors hidden sm:inline-flex">
                            Sign Up
                        </button>
                    @endauth

                    <!-- Cart -->
                    @php
                        $cartController = app(\App\Http\Controllers\CartController::class);
                        $cartItems = $cartController->getCartItems();
                        $cartTotal = $cartItems->sum('subtotal');
                        $cartCount = $cartItems->sum('quantity');
                    @endphp
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-700 hover:text-amber-600 transition-colors" id="cart-link">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                        </svg>
                        <span id="cart-count-badge" data-cart-count="{{ $cartCount }}" class="absolute -top-1 -right-1 bg-amber-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center {{ $cartCount > 0 ? '' : 'hidden' }}">
                            {{ $cartCount }}
                        </span>
                    </a>

                    <!-- Mobile menu button -->
                    <button @click="mobileOpen = !mobileOpen" type="button" class="md:hidden p-2 rounded-md text-gray-700 hover:text-amber-600 hover:bg-gray-100 transition-colors">
                        <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="md:hidden border-t border-gray-200 bg-white">
            <div class="px-4 pt-3 pb-4 space-y-1">
                <!-- Mobile Search -->
                <form action="{{ route('products.index') }}" method="GET" class="mb-3">
                    <input type="text" name="search" placeholder="Search coffee..." value="{{ request('search') }}"
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-amber-500 bg-gray-50">
                </form>

                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('home') ? 'text-amber-600 bg-amber-50' : 'text-gray-700 hover:bg-gray-50' }}">Home</a>
                <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('products.*') ? 'text-amber-600 bg-amber-50' : 'text-gray-700 hover:bg-gray-50' }}">Shop</a>
                <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('about') ? 'text-amber-600 bg-amber-50' : 'text-gray-700 hover:bg-gray-50' }}">About</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('contact') ? 'text-amber-600 bg-amber-50' : 'text-gray-700 hover:bg-gray-50' }}">Contact</a>

                @guest('customer')
                    <div class="pt-3 border-t border-gray-200 flex gap-3">
                        <button onclick="openLoginModal()" class="flex-1 text-center px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 hover:bg-gray-50">Sign In</button>
                        <button onclick="openRegisterModal()" class="flex-1 text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-full text-sm font-medium">Sign Up</button>
                    </div>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed top-20 right-4 z-50 max-w-sm">
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3 rounded-lg shadow-lg">
                <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <p class="text-sm font-medium">{{ session('success') }}</p>
                <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed top-20 right-4 z-50 max-w-sm">
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-lg shadow-lg">
                <svg class="h-5 w-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <p class="text-sm font-medium">{{ session('error') }}</p>
                <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Auth Modals -->
    @include('components.auth-modals')

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Footer -->
            <div class="py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="lg:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center mb-4">
                        <img class="h-8 w-auto brightness-200" src="/images/logo.png" alt="High Elevation Coffee">
                        <span class="ml-2 text-xl font-bold text-white">High Elevation</span>
                    </a>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Premium coffee beans sourced from the world's finest high-altitude regions, roasted to perfection and delivered fresh to your door.
                    </p>
                    <div class="mt-4 flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-amber-400 transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-amber-400 transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-amber-400 transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Shop</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">All Products</a></li>
                        <li><a href="{{ route('about') }}" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Customer Service</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">Shipping & Delivery</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">Returns & Refunds</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">FAQ</a></li>
                        <li><a href="{{ route('contact') }}" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">Support</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">Shipping Policy</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-amber-400 transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-500">&copy; {{ date('Y') }} High Elevation Coffee. All rights reserved.</p>
                <div class="flex items-center space-x-4">
                    <img src="https://img.icons8.com/color/32/visa.png" alt="Visa" class="h-6 opacity-60">
                    <img src="https://img.icons8.com/color/32/mastercard-logo.png" alt="Mastercard" class="h-6 opacity-60">
                    <img src="https://img.icons8.com/color/32/paypal.png" alt="PayPal" class="h-6 opacity-60">
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>