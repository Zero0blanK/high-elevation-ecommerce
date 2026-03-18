@extends('admin.layouts.app')

@section('title', $customer->first_name . ' ' . $customer->last_name)

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8" x-data="{ showDeleteModal: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-full bg-amber-100 flex items-center justify-center ring-4 ring-amber-50">
                <span class="text-xl font-bold text-amber-700">{{ strtoupper(substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1)) }}</span>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</h1>
                    @if($customer->is_active)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/20">
                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                            Inactive
                        </span>
                    @endif
                </div>
                <p class="mt-0.5 text-sm text-gray-500">Customer since {{ $customer->created_at->format('F d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.index') }}"
               class="inline-flex items-center px-3.5 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <a href="{{ route('admin.customers.edit', $customer) }}"
               class="inline-flex items-center px-3.5 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-600 transition-colors">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $analytics['total_orders'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Spent</p>
                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($analytics['total_spent'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Avg Order Value</p>
                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($analytics['avg_order_value'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Member Since</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $customer->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Customer Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Customer Information</h2>
                <dl class="space-y-4">
                    <div class="flex items-start gap-3">
                        <dt class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </dt>
                        <dd>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</p>
                            <p class="mt-0.5 text-sm text-gray-900">{{ $customer->email }}</p>
                        </dd>
                    </div>
                    <div class="flex items-start gap-3">
                        <dt class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </dt>
                        <dd>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</p>
                            <p class="mt-0.5 text-sm text-gray-900">{{ $customer->phone ?? '—' }}</p>
                        </dd>
                    </div>
                    <div class="flex items-start gap-3">
                        <dt class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.75 1.75 0 013 15.546M12 2v4m0 0a2 2 0 100 4 2 2 0 000-4z"/>
                            </svg>
                        </dt>
                        <dd>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</p>
                            <p class="mt-0.5 text-sm text-gray-900">{{ $customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('F d, Y') : '—' }}</p>
                        </dd>
                    </div>
                    <div class="flex items-start gap-3">
                        <dt class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </dt>
                        <dd>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</p>
                            <div class="mt-1">
                                @if($customer->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Active</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/20">Inactive</span>
                                @endif
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Coffee Preferences --}}
            @if($customer->preferences)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            Coffee Preferences
                        </span>
                    </h2>
                    <dl class="space-y-3">
                        @if($customer->preferences->preferred_roast)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <dt class="text-sm text-gray-500">Preferred Roast</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ ucfirst($customer->preferences->preferred_roast) }}</dd>
                            </div>
                        @endif
                        @if($customer->preferences->preferred_grind)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <dt class="text-sm text-gray-500">Preferred Grind</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ ucfirst($customer->preferences->preferred_grind) }}</dd>
                            </div>
                        @endif
                        @if($customer->preferences->flavor_preferences)
                            <div class="py-2 border-b border-gray-100">
                                <dt class="text-sm text-gray-500 mb-2">Flavor Preferences</dt>
                                <dd class="flex flex-wrap gap-1.5">
                                    @foreach((array) $customer->preferences->flavor_preferences as $flavor)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-600/20">{{ ucfirst($flavor) }}</span>
                                    @endforeach
                                </dd>
                            </div>
                        @endif
                        @if($customer->preferences->brewing_method)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <dt class="text-sm text-gray-500">Brewing Method</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ ucfirst($customer->preferences->brewing_method) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between items-center py-2">
                            <dt class="text-sm text-gray-500">Newsletter</dt>
                            <dd>
                                @if($customer->preferences->newsletter_subscribed)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Subscribed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-600">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Not Subscribed
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            @endif

            {{-- Addresses --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Addresses</h2>
                @if($customer->addresses->count())
                    <div class="space-y-3">
                        @foreach($customer->addresses as $address)
                            <div class="rounded-lg border border-gray-200 p-4 {{ $address->is_default ? 'bg-amber-50/50 border-amber-200' : '' }}">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $address->type === 'shipping' ? 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10' : 'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-700/10' }}">
                                        {{ ucfirst($address->type) }}
                                    </span>
                                    @if($address->is_default)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                            <svg class="mr-0.5 h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"/></svg>
                                            Default
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ $address->first_name }} {{ $address->last_name }}</p>
                                <p class="text-sm text-gray-600 mt-0.5">{{ $address->address_line_1 }}</p>
                                @if($address->address_line_2)
                                    <p class="text-sm text-gray-600">{{ $address->address_line_2 }}</p>
                                @endif
                                <p class="text-sm text-gray-600">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                <p class="text-sm text-gray-600">{{ $address->country }}</p>
                                @if($address->phone)
                                    <p class="text-sm text-gray-500 mt-1">{{ $address->phone }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No addresses on file.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Recent Orders --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Recent Orders</h2>
                    @if($customer->orders->count() > 0)
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-md">Last 10</span>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order #</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($customer->orders->take(10) as $order)
                                <tr class="hover:bg-amber-50/40 transition-colors">
                                    <td class="px-6 py-3.5 text-sm">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-amber-600 hover:text-amber-800 transition-colors">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-3.5 text-sm font-medium text-gray-900 text-right">₱{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="px-6 py-3.5">
                                        @php
                                            $statusStyles = [
                                                'pending'    => 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
                                                'processing' => 'bg-blue-50 text-blue-700 ring-blue-700/10',
                                                'shipped'    => 'bg-indigo-50 text-indigo-700 ring-indigo-700/10',
                                                'delivered'  => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                                'cancelled'  => 'bg-red-50 text-red-700 ring-red-600/10',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1 ring-inset {{ $statusStyles[$order->status] ?? 'bg-gray-50 text-gray-600 ring-gray-500/10' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="h-10 w-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-900">No orders yet</p>
                                            <p class="mt-1 text-sm text-gray-500">This customer hasn't placed any orders.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Actions</h2>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.customers.edit', $customer) }}"
                       class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="-ml-0.5 mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Customer
                    </a>

                    <form method="POST" action="{{ route('admin.customers.send-email') }}" class="inline">
                        @csrf
                        <input type="hidden" name="customer_ids[]" value="{{ $customer->id }}">
                        <input type="hidden" name="subject" value="">
                        <input type="hidden" name="content" value="">
                        <button type="button"
                                onclick="document.getElementById('quick-email-subject').value && this.closest('form').submit()"
                                class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="-ml-0.5 mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Send Email
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="first_name" value="{{ $customer->first_name }}">
                        <input type="hidden" name="last_name" value="{{ $customer->last_name }}">
                        <input type="hidden" name="email" value="{{ $customer->email }}">
                        <input type="hidden" name="is_active" value="{{ $customer->is_active ? '0' : '1' }}">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2.5 border shadow-sm text-sm font-medium rounded-lg transition-colors {{ $customer->is_active ? 'border-yellow-300 text-yellow-800 bg-yellow-50 hover:bg-yellow-100' : 'border-emerald-300 text-emerald-800 bg-emerald-50 hover:bg-emerald-100' }}">
                            @if($customer->is_active)
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Deactivate
                            @else
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Activate
                            @endif
                        </button>
                    </form>

                    <button @click="showDeleteModal = true"
                            class="inline-flex items-center px-4 py-2.5 border border-red-300 shadow-sm text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 transition-colors">
                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showDeleteModal = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-xl shadow-2xl max-w-md w-full z-10 p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Delete Customer</h3>
                        <p class="mt-1 text-sm text-gray-500">Are you sure you want to delete <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong>? This action cannot be undone.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 transition-colors">
                            Delete Customer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
