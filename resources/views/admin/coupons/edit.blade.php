@extends('admin.layouts.app')

@section('title', 'Edit Coupon - ' . $coupon->code)

@section('content')
<div x-data="{
    discountType: '{{ old('type', $coupon->type) }}',
    generateCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let p1 = '', p2 = '';
        for (let i = 0; i < 4; i++) {
            p1 += chars.charAt(Math.floor(Math.random() * chars.length));
            p2 += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('code').value = p1 + '-' + p2;
    }
}">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.coupons.show', $coupon) }}" class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Coupon</h1>
                <p class="text-sm text-gray-500 font-mono mt-0.5">{{ $coupon->code }}</p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            {{-- Usage Info Banner --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100">
                        <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-amber-800">
                            This coupon has been used <span class="font-bold">{{ $coupon->usage_count ?? 0 }}</span> time(s)
                        </p>
                        <p class="text-xs text-amber-600 mt-0.5">Created on {{ $coupon->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            </div>

            {{-- Section: Coupon Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Coupon Details</h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Coupon Code <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}" required placeholder="e.g. BREW-2024" class="flex-1 border-gray-300 rounded-l-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm uppercase font-mono tracking-wider @error('code') border-red-300 @enderror">
                                <button type="button" @click="generateCode()" class="inline-flex items-center gap-1.5 px-4 border border-l-0 border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 rounded-r-lg hover:bg-gray-100 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Generate
                                </button>
                            </div>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" name="description" id="description" value="{{ old('description', $coupon->description) }}" placeholder="Brief description of the coupon" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('description') border-red-300 @enderror">
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section: Discount Settings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Discount Settings</h2>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Type Radio --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex items-center gap-3 cursor-pointer rounded-lg border p-4 transition-colors" :class="discountType === 'fixed_amount' ? 'border-amber-500 bg-amber-50 ring-1 ring-amber-500' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="type" value="fixed_amount" x-model="discountType" class="sr-only">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg" :class="discountType === 'fixed_amount' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500'">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-900">Fixed Amount</span>
                                    <p class="text-xs text-gray-500">Deduct a specific amount</p>
                                </div>
                            </label>
                            <label class="relative flex items-center gap-3 cursor-pointer rounded-lg border p-4 transition-colors" :class="discountType === 'percentage' ? 'border-amber-500 bg-amber-50 ring-1 ring-amber-500' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="type" value="percentage" x-model="discountType" class="sr-only">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg" :class="discountType === 'percentage' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500'">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-900">Percentage</span>
                                    <p class="text-xs text-gray-500">Deduct a percentage of total</p>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700 mb-1">
                                <span x-text="discountType === 'percentage' ? 'Percentage Value' : 'Discount Amount'">Discount Amount</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-400 text-sm" x-text="discountType === 'percentage' ? '%' : '₱'">₱</span>
                                </div>
                                <input type="number" name="value" id="value" value="{{ old('value', $coupon->value) }}" required step="0.01" min="0" placeholder="0.00" class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('value') border-red-300 @enderror">
                            </div>
                            @error('value')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="minimum_amount" class="block text-sm font-medium text-gray-700 mb-1">Minimum Order Amount</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-400 text-sm">₱</span>
                                </div>
                                <input type="number" name="minimum_amount" id="minimum_amount" value="{{ old('minimum_amount', $coupon->minimum_amount) }}" step="0.01" min="0" placeholder="0.00" class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('minimum_amount') border-red-300 @enderror">
                            </div>
                            @error('minimum_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div x-show="discountType === 'percentage'" x-transition>
                            <label for="maximum_discount" class="block text-sm font-medium text-gray-700 mb-1">Maximum Discount</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-400 text-sm">₱</span>
                                </div>
                                <input type="number" name="maximum_discount" id="maximum_discount" value="{{ old('maximum_discount', $coupon->maximum_discount) }}" step="0.01" min="0" placeholder="No limit" class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('maximum_discount') border-red-300 @enderror">
                            </div>
                            @error('maximum_discount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section: Usage Limits --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Usage Limits</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-1">Total Usage Limit</label>
                            <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="0" placeholder="Unlimited" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('usage_limit') border-red-300 @enderror">
                            <p class="mt-1 text-xs text-gray-400">Leave empty for unlimited usage</p>
                            @error('usage_limit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="usage_limit_per_customer" class="block text-sm font-medium text-gray-700 mb-1">Per Customer Limit</label>
                            <input type="number" name="usage_limit_per_customer" id="usage_limit_per_customer" value="{{ old('usage_limit_per_customer', $coupon->usage_limit_per_customer) }}" min="0" placeholder="Unlimited" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('usage_limit_per_customer') border-red-300 @enderror">
                            <p class="mt-1 text-xs text-gray-400">Max uses per individual customer</p>
                            @error('usage_limit_per_customer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section: Schedule --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Schedule</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                            <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('starts_at') border-red-300 @enderror">
                            <p class="mt-1 text-xs text-gray-400">Leave empty to start immediately</p>
                            @error('starts_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                            <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('expires_at') border-red-300 @enderror">
                            <p class="mt-1 text-xs text-gray-400">Leave empty for no expiration</p>
                            @error('expires_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section: Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Status</h2>
                </div>
                <div class="p-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-400 ml-14">Enable this coupon for immediate use</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.coupons.show', $coupon) }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-4 py-2 rounded-lg transition-colors text-sm">Cancel</a>
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-6 py-2 rounded-lg transition-colors text-sm inline-flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Coupon
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
