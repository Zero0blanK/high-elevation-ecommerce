<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('ecommerce.store.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body class="font-inter antialiased bg-gray-50" x-data="{ sidebarOpen: false }">
    <div id="app" class="min-h-screen flex">

        {{-- Mobile sidebar overlay --}}
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 md:hidden" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="sidebarOpen" @click="sidebarOpen = false"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/80"></div>

            {{-- Slide-in panel --}}
            <div x-show="sidebarOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="relative flex w-full max-w-[17rem] flex-col bg-gray-900">
                {{-- Close button --}}
                <div class="absolute top-0 right-0 -mr-12 pt-4">
                    <button @click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                @include('admin.partials.sidebar')
            </div>
        </div>

        {{-- Desktop sidebar --}}
        <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col">
            @include('admin.partials.sidebar')
        </div>

        {{-- Main column --}}
        <div class="flex flex-1 flex-col md:pl-64">
            {{-- Main content --}}
            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </main>
        </div>
    </div>

    @php
        $adminToasts = collect([
            session('success') ? ['type' => 'success', 'message' => session('success')] : null,
            session('error') ? ['type' => 'error', 'message' => session('error')] : null,
            session('warning') ? ['type' => 'warning', 'message' => session('warning')] : null,
            session('info') ? ['type' => 'info', 'message' => session('info')] : null,
        ])->filter()->values();
    @endphp

    <div id="admin-toast-container" class="pointer-events-none fixed top-4 right-0 z-[100] flex w-full max-w-sm flex-col gap-2 px-4"></div>
    <script>
        (() => {
            const container = document.getElementById('admin-toast-container');
            if (!container) return;

            const typeClasses = {
                success: 'bg-emerald-600',
                error: 'bg-red-600',
                warning: 'bg-amber-500',
                info: 'bg-sky-600',
            };

            window.adminToast = (message, type = 'info') => {
                if (!message) return;

                const toast = document.createElement('div');
                toast.className = `pointer-events-auto flex items-center justify-between gap-3 rounded-lg px-4 py-3 text-sm text-white shadow-lg transition-all duration-300 ease-out translate-x-6 opacity-0 ${typeClasses[type] ?? typeClasses.info}`;

                const text = document.createElement('p');
                text.className = 'font-medium';
                text.textContent = message;

                const closeButton = document.createElement('button');
                closeButton.type = 'button';
                closeButton.className = 'rounded p-0.5 text-white/80 hover:text-white';
                closeButton.setAttribute('aria-label', 'Dismiss notification');
                closeButton.innerHTML = '&times;';

                closeButton.addEventListener('click', () => {
                    toast.classList.add('translate-x-6', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                });

                toast.appendChild(text);
                toast.appendChild(closeButton);
                container.appendChild(toast);

                requestAnimationFrame(() => toast.classList.remove('translate-x-6', 'opacity-0'));

                setTimeout(() => {
                    toast.classList.add('translate-x-6', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, 3500);
            };

            const flashToasts = @json($adminToasts);
            flashToasts.forEach((toast) => window.adminToast(toast.message, toast.type));

            @if($errors->any())
                window.adminToast(@json($errors->first()), 'error');
            @endif
        })();
    </script>

    @include('components.confirm-modal')
    @stack('scripts')
</body>
</html>
