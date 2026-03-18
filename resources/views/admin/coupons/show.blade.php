@extends('admin.layouts.app')

@section('title', 'Coupon: ' . $coupon->code)

@section('content')
<div x-data="{ showDeleteModal: false }">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold font-mono tracking-wider text-gray-900">{{ $coupon->code }}</h1>
                    @php
                        $statusConfig = [
                            'active'    => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'inactive'  => 'bg-gray-50 text-gray-600 ring-gray-500/20',
                            'expired'   => 'bg-red-50 text-red-700 ring-red-600/20',
                            'scheduled' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                            'used_up'   => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $statusConfig[$coupon->status] ?? 'bg-gray-50 text-gray-600 ring-gray-500/20' }}">
                        {{ ucfirst(str_replace('_', ' ', $coupon->status)) }}
                    </span>
                </div>
                @if($coupon->description)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $coupon->description }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <button @click="showDeleteModal = true" class="inline-flex items-center gap-1.5 bg-white border border-red-300 text-red-700 hover:bg-red-50 font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </div>
    </div>

    {{-- Visual Coupon Card --}}
    <div class="relative mb-6">
        <div class="bg-gradient-to-r from-amber-600 to-amber-500 rounded-xl shadow-lg overflow-hidden">
            {{-- Scalloped edges --}}
            <div class="absolute top-0 bottom-0 left-0 flex flex-col justify-around -ml-3">
                @for($i = 0; $i < 6; $i++)
                    <div class="w-6 h-6 bg-gray-50 rounded-full"></div>
                @endfor
            </div>
            <div class="absolute top-0 bottom-0 right-0 flex flex-col justify-around -mr-3">
                @for($i = 0; $i < 6; $i++)
                    <div class="w-6 h-6 bg-gray-50 rounded-full"></div>
                @endfor
            </div>

            <div class="px-10 py-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="text-center sm:text-left">
                    <p class="text-amber-100 text-xs font-semibold uppercase tracking-widest mb-1">Discount Coupon</p>
                    <p class="text-white text-4xl sm:text-5xl font-bold tracking-tight">
                        @if($coupon->type === 'percentage')
                            {{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}% OFF
                        @else
                            ₱{{ number_format($coupon->value, 2) }} OFF
                        @endif
                    </p>
                    @if($coupon->minimum_amount)
                        <p class="text-amber-100 text-sm mt-2">On orders over ₱{{ number_format($coupon->minimum_amount, 2) }}</p>
                    @endif
                </div>
                <div class="text-center sm:text-right">
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-5 py-3 border border-white/30">
                        <p class="text-amber-100 text-xs font-medium uppercase tracking-wider mb-1">Code</p>
                        <p class="text-white text-2xl font-bold font-mono tracking-widest">{{ $coupon->code }}</p>
                    </div>
                    @if($coupon->expires_at)
                        <p class="text-amber-100 text-xs mt-2">Valid until {{ $coupon->expires_at->format('M d, Y') }}</p>
                    @else
                        <p class="text-amber-100 text-xs mt-2">No expiration date</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100">
                    <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Discount</p>
                    <p class="text-lg font-bold text-gray-900">
                        @if($coupon->type === 'percentage')
                            {{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}%
                        @else
                            ₱{{ number_format($coupon->value, 2) }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Times Used</p>
                    <p class="text-lg font-bold text-gray-900">{{ $coupon->usage_count ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Savings</p>
                    <p class="text-lg font-bold text-gray-900">₱{{ number_format($coupon->orders->sum('discount_amount'), 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Created</p>
                    <p class="text-lg font-bold text-gray-900">{{ $coupon->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Usage Progress Bar --}}
    @if($coupon->usage_limit)
        @php $usagePct = min(100, (($coupon->usage_count ?? 0) / $coupon->usage_limit) * 100); @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-700">Usage Progress</p>
                <p class="text-sm text-gray-500">
                    <span class="font-semibold text-gray-900">{{ $coupon->usage_count ?? 0 }}</span> / {{ $coupon->usage_limit }} uses
                </p>
            </div>
            <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 {{ $usagePct >= 90 ? 'bg-red-500' : ($usagePct >= 60 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $usagePct }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1.5">{{ number_format($usagePct, 0) }}% of total limit used</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Coupon Details --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Coupon Details</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Type</dt>
                            <dd>
                                @if($coupon->type === 'percentage')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20">Percentage</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Fixed Amount</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Value</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                @if($coupon->type === 'percentage')
                                    {{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}%
                                @else
                                    ₱{{ number_format($coupon->value, 2) }}
                                @endif
                            </dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Min. Order</dt>
                            <dd class="text-sm text-gray-900">{{ $coupon->minimum_amount ? '₱' . number_format($coupon->minimum_amount, 2) : '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Max. Discount</dt>
                            <dd class="text-sm text-gray-900">{{ $coupon->maximum_discount ? '₱' . number_format($coupon->maximum_discount, 2) : '—' }}</dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Usage Limit</dt>
                            <dd class="text-sm text-gray-900">{{ $coupon->usage_limit ?? 'Unlimited' }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Per Customer</dt>
                            <dd class="text-sm text-gray-900">{{ $coupon->usage_limit_per_customer ?? 'Unlimited' }}</dd>
                        </div>
                        <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Starts At</dt>
                            <dd class="text-sm text-gray-900">{{ $coupon->starts_at ? $coupon->starts_at->format('M d, Y H:i') : 'Immediately' }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Expires At</dt>
                            <dd class="text-sm text-gray-900">{{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y H:i') : 'Never' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Orders Using This Coupon --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Orders Using This Coupon</h2>
                    <span class="text-xs text-gray-400">{{ $coupon->orders->count() }} total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/75">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Discount</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($coupon->orders->take(10) as $order)
                                <tr class="hover:bg-amber-50/40 transition-colors">
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-amber-600 hover:text-amber-800 font-medium">{{ $order->order_number }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $order->customer->full_name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $orderStatusColors = [
                                                'pending'    => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                                'processing' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                                'shipped'    => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
                                                'delivered'  => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                                'cancelled'  => 'bg-red-50 text-red-700 ring-red-600/20',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $orderStatusColors[$order->status] ?? 'bg-gray-50 text-gray-600 ring-gray-500/20' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-red-600 text-right font-medium">-₱{{ number_format($order->discount_amount, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right font-semibold">₱{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            </div>
                                            <p class="text-sm text-gray-500">No orders have used this coupon yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showDeleteModal" @click="showDeleteModal = false"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 mb-4">
                        <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Coupon</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Are you sure you want to delete coupon <span class="font-mono font-semibold text-gray-700">{{ $coupon->code }}</span>?
                        This action cannot be undone.
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <button @click="showDeleteModal = false" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                            Cancel
                        </button>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete Coupon
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
