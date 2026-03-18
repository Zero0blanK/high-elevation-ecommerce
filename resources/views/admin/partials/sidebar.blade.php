<div class="flex h-full flex-col bg-gray-900">
    {{-- Logo --}}
    <div class="flex h-16 shrink-0 items-center gap-x-3 px-6 border-b border-gray-800">
        <svg class="h-8 w-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" />
        </svg>
        <span class="text-lg font-bold text-amber-500 tracking-tight">High Elevation</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">

        {{-- MAIN --}}
        <div>
            <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Main</p>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       @class([
                           'group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                           'bg-amber-600/20 text-amber-500 border-l-[3px] border-amber-500' => request()->routeIs('admin.dashboard'),
                           'text-gray-300 hover:bg-gray-800 hover:text-white border-l-[3px] border-transparent' => !request()->routeIs('admin.dashboard'),
                       ])>
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        Dashboard
                    </a>
                </li>
            </ul>
        </div>

        {{-- MANAGEMENT --}}
        <div>
            <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Management</p>
            <ul class="space-y-1">
                {{-- Products (with submenu) --}}
                <li x-data="{ open: {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            @class([
                                'group w-full flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                'bg-amber-600/20 text-amber-500 border-l-[3px] border-amber-500' => request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*'),
                                'text-gray-300 hover:bg-gray-800 hover:text-white border-l-[3px] border-transparent' => !request()->routeIs('admin.products.*') && !request()->routeIs('admin.categories.*'),
                            ])>
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span class="flex-1 text-left">Products</span>
                        <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak x-transition.duration.200ms class="mt-1 space-y-1 pl-10">
                        <li>
                            <a href="{{ route('admin.products.index') }}"
                               @class([
                                   'flex items-center gap-x-2 rounded-lg px-3 py-1.5 text-sm transition-colors',
                                   'text-amber-500 font-medium' => request()->routeIs('admin.products.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('admin.products.*'),
                               ])>
                                <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.products.*') ? 'bg-amber-500' : 'bg-gray-600' }}"></span>
                                All Products
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index') }}"
                               @class([
                                   'flex items-center gap-x-2 rounded-lg px-3 py-1.5 text-sm transition-colors',
                                   'text-amber-500 font-medium' => request()->routeIs('admin.categories.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('admin.categories.*'),
                               ])>
                                <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.categories.*') ? 'bg-amber-500' : 'bg-gray-600' }}"></span>
                                Categories
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Orders --}}
                <li>
                    <a href="{{ route('admin.orders.index') }}"
                       @class([
                           'group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                           'bg-amber-600/20 text-amber-500 border-l-[3px] border-amber-500' => request()->routeIs('admin.orders.*'),
                           'text-gray-300 hover:bg-gray-800 hover:text-white border-l-[3px] border-transparent' => !request()->routeIs('admin.orders.*'),
                       ])>
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <span class="flex-1">Orders</span>
                        @if(($pendingOrdersCount ?? 0) > 0)
                            <span class="inline-flex items-center rounded-full bg-red-500/20 px-2 py-0.5 text-xs font-medium text-red-400">
                                {{ $pendingOrdersCount ?? 0 }}
                            </span>
                        @endif
                    </a>
                </li>

                {{-- Customers --}}
                <li>
                    <a href="{{ route('admin.customers.index') }}"
                       @class([
                           'group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                           'bg-amber-600/20 text-amber-500 border-l-[3px] border-amber-500' => request()->routeIs('admin.customers.*'),
                           'text-gray-300 hover:bg-gray-800 hover:text-white border-l-[3px] border-transparent' => !request()->routeIs('admin.customers.*'),
                       ])>
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Customers
                    </a>
                </li>

                {{-- Inventory (with submenu) --}}
                <li x-data="{ open: {{ request()->routeIs('admin.inventory.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            @class([
                                'group w-full flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                'bg-amber-600/20 text-amber-500 border-l-[3px] border-amber-500' => request()->routeIs('admin.inventory.*'),
                                'text-gray-300 hover:bg-gray-800 hover:text-white border-l-[3px] border-transparent' => !request()->routeIs('admin.inventory.*'),
                            ])>
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3" />
                        </svg>
                        <span class="flex-1 text-left">Inventory</span>
                        @if(($lowStockCount ?? 0) > 0)
                            <span class="inline-flex items-center rounded-full bg-amber-500/20 px-2 py-0.5 text-xs font-medium text-amber-400 mr-1">
                                {{ $lowStockCount ?? 0 }}
                            </span>
                        @endif
                        <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak x-transition.duration.200ms class="mt-1 space-y-1 pl-10">
                        <li>
                            <a href="{{ route('admin.inventory.index') }}"
                               @class([
                                   'flex items-center gap-x-2 rounded-lg px-3 py-1.5 text-sm transition-colors',
                                   'text-amber-500 font-medium' => request()->routeIs('admin.inventory.index'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('admin.inventory.index'),
                               ])>
                                <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.inventory.index') ? 'bg-amber-500' : 'bg-gray-600' }}"></span>
                                Overview
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.inventory.stock-in') }}"
                               @class([
                                   'flex items-center gap-x-2 rounded-lg px-3 py-1.5 text-sm transition-colors',
                                   'text-amber-500 font-medium' => request()->routeIs('admin.inventory.stock-in'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('admin.inventory.stock-in'),
                               ])>
                                <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.inventory.stock-in') ? 'bg-amber-500' : 'bg-gray-600' }}"></span>
                                Stock In
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.inventory.stock-out') }}"
                               @class([
                                   'flex items-center gap-x-2 rounded-lg px-3 py-1.5 text-sm transition-colors',
                                   'text-amber-500 font-medium' => request()->routeIs('admin.inventory.stock-out'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('admin.inventory.stock-out'),
                               ])>
                                <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.inventory.stock-out') ? 'bg-amber-500' : 'bg-gray-600' }}"></span>
                                Stock Out
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.inventory.logs') }}"
                               @class([
                                   'flex items-center gap-x-2 rounded-lg px-3 py-1.5 text-sm transition-colors',
                                   'text-amber-500 font-medium' => request()->routeIs('admin.inventory.logs'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('admin.inventory.logs'),
                               ])>
                                <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.inventory.logs') ? 'bg-amber-500' : 'bg-gray-600' }}"></span>
                                Activity Log
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Coupons --}}
                <li>
                    <a href="{{ route('admin.coupons.index') }}"
                       @class([
                           'group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                           'bg-amber-600/20 text-amber-500 border-l-[3px] border-amber-500' => request()->routeIs('admin.coupons.*'),
                           'text-gray-300 hover:bg-gray-800 hover:text-white border-l-[3px] border-transparent' => !request()->routeIs('admin.coupons.*'),
                       ])>
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                        Coupons
                    </a>
                </li>

                {{-- Audit Logs --}}
                <li>
                    <a href="{{ route('admin.audit-logs.index') }}"
                       @class([
                           'group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                           'bg-amber-600/20 text-amber-500 border-l-[3px] border-amber-500' => request()->routeIs('admin.audit-logs.*'),
                           'text-gray-300 hover:bg-gray-800 hover:text-white border-l-[3px] border-transparent' => !request()->routeIs('admin.audit-logs.*'),
                       ])>
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Audit Logs
                    </a>
                </li>
            </ul>
        </div>

        {{-- REPORTS --}}
        <div>
            <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Reports</p>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.analytics.index') }}"
                       @class([
                           'group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                           'bg-amber-600/20 text-amber-500 border-l-[3px] border-amber-500' => request()->routeIs('admin.analytics.*'),
                           'text-gray-300 hover:bg-gray-800 hover:text-white border-l-[3px] border-transparent' => !request()->routeIs('admin.analytics.*'),
                       ])>
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        Analytics
                    </a>
                </li>
            </ul>
        </div>

        {{-- SYSTEM (admin only) --}}
        @if(auth('admin')->user()->isAdmin())
            <div>
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">System</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.admin-users.index') }}"
                           @class([
                               'group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                               'bg-amber-600/20 text-amber-500 border-l-[3px] border-amber-500' => request()->routeIs('admin.admin-users.*'),
                               'text-gray-300 hover:bg-gray-800 hover:text-white border-l-[3px] border-transparent' => !request()->routeIs('admin.admin-users.*'),
                           ])>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Admin Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.index') }}"
                           @class([
                               'group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                               'bg-amber-600/20 text-amber-500 border-l-[3px] border-amber-500' => request()->routeIs('admin.settings.*'),
                               'text-gray-300 hover:bg-gray-800 hover:text-white border-l-[3px] border-transparent' => !request()->routeIs('admin.settings.*'),
                           ])>
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                    </li>
                </ul>
            </div>
        @endif
    </nav>

    {{-- User section --}}
    <div class="shrink-0 border-t border-gray-800 p-4">
        <div class="flex items-center gap-x-3">
            <img class="h-9 w-9 rounded-full bg-gray-700 object-cover ring-2 ring-gray-700"
                 src="https://ui-avatars.com/api/?name={{ urlencode(auth('admin')->user()->name) }}&color=D97706&background=292524"
                 alt="{{ auth('admin')->user()->name }}">
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-gray-200">{{ auth('admin')->user()->name }}</p>
                <p class="truncate text-xs text-gray-500 capitalize">{{ auth('admin')->user()->role }}</p>
            </div>
        </div>
        <div class="mt-3 flex items-center gap-x-2">
            <a href="{{ route('admin.profile.show') }}"
               class="flex-1 rounded-lg bg-gray-800 px-3 py-1.5 text-center text-xs font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                Profile
            </a>
            <form method="POST" action="{{ route('admin.logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full rounded-lg bg-gray-800 px-3 py-1.5 text-center text-xs font-medium text-gray-300 hover:bg-red-600/20 hover:text-red-400 transition-colors">
                    Sign out
                </button>
            </form>
        </div>
    </div>
</div>
