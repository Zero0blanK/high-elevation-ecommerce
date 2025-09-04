<div class="flex-1 flex flex-col min-h-0 bg-white shadow">
    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
        <div class="flex items-center flex-shrink-0 px-4">
            <svg class="h-8 w-8 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2L3 7v11h14V7l-7-5z"/>
            </svg>
            <h1 class="ml-2 text-xl font-bold text-gray-900">Admin Panel</h1>
        </div>
        
        <nav class="mt-5 flex-1 px-2 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               @class([
                   'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
                   'bg-amber-100 text-amber-900' => request()->routeIs('admin.dashboard'),
                   'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.dashboard')
               ])>
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.dashboard') ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v6H8V5z"/>
                </svg>
                Dashboard
            </a>

            <!-- Products -->
            <div x-data="{ open: {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Products
                    <svg class="ml-auto h-5 w-5 transform transition-transform" :class="{ 'rotate-90': open }" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div x-show="open" class="space-y-1 px-7">
                    <a href="{{ route('admin.products.index') }}" 
                       @class([
                           'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
                           'bg-amber-100 text-amber-900' => request()->routeIs('admin.products.*'),
                           'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.products.*')
                       ])>
                        All Products
                    </a>
                    <a href="{{ route('admin.categories.index') }}" 
                       @class([
                           'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
                           'bg-amber-100 text-amber-900' => request()->routeIs('admin.categories.*'),
                           'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.categories.*')
                       ])>
                        Categories
                    </a>
                </div>
            </div>

            <!-- Orders -->
            <a href="{{ route('admin.orders.index') }}" 
               @class([
                   'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
                   'bg-amber-100 text-amber-900' => request()->routeIs('admin.orders.*'),
                   'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.orders.*')
               ])>
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.orders.*') ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Orders
                @if($pendingOrdersCount ?? 0 > 0)
                    <span class="ml-auto bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ $pendingOrdersCount }}
                    </span>
                @endif
            </a>

            <!-- Customers -->
            <a href="{{ route('admin.customers.index') }}" 
               @class([
                   'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
                   'bg-amber-100 text-amber-900' => request()->routeIs('admin.customers.*'),
                   'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.customers.*')
               ])>
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.customers.*') ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                Customers
            </a>

            <!-- Inventory -->
            <a href="{{ route('admin.inventory.index') }}" 
               @class([
                   'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
                   'bg-amber-100 text-amber-900' => request()->routeIs('admin.inventory.*'),
                   'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.inventory.*')
               ])>
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.inventory.*') ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Inventory
                @if($lowStockCount ?? 0 > 0)
                    <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ $lowStockCount }}
                    </span>
                @endif
            </a>

            <!-- Analytics -->
            <a href="{{ route('admin.analytics.index') }}" 
               @class([
                   'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
                   'bg-amber-100 text-amber-900' => request()->routeIs('admin.analytics.*'),
                   'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.analytics.*')
               ])>
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.analytics.*') ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Analytics
            </a>

            <!-- Coupons -->
            <a href="{{ route('admin.coupons.index') }}" 
               @class([
                   'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
                   'bg-amber-100 text-amber-900' => request()->routeIs('admin.coupons.*'),
                   'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.coupons.*')
               ])>
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.coupons.*') ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Coupons
            </a>

            <!-- Settings -->
            @if(auth('admin')->user()->isAdmin())
                <a href="{{ route('admin.settings.index') }}" 
                   @class([
                       'group flex items-center px-2 py-2 text-sm font-medium rounded-md',
                       'bg-amber-100 text-amber-900' => request()->routeIs('admin.settings.*'),
                       'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('admin.settings.*')
                   ])>
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.settings.*') ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
            @endif
        </nav>
    </div>
    
    <!-- User menu -->
    <div class="flex-shrink-0 flex bg-gray-50 p-4">
        <div class="flex-shrink-0 w-full group block" x-data="{ open: false }">
            <div class="flex items-center">
                <div>
                    <img class="inline-block h-9 w-9 rounded-full bg-gray-300" src="https://ui-avatars.com/api/?name={{ urlencode(auth('admin')->user()->name) }}&color=7C3AED&background=EDE9FE" alt="{{ auth('admin')->user()->name }}">
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ auth('admin')->user()->name }}</p>
                    <p class="text-xs font-medium text-gray-500 group-hover:text-gray-700 capitalize">{{ auth('admin')->user()->role }}</p>
                </div>
                <div class="ml-auto">
                    <button @click="open = !open" class="bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div x-show="open" @click.outside="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                <a href="{{ route('admin.profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
