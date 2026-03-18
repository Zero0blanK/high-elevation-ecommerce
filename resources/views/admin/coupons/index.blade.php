@extends('admin.layouts.app')

@section('title', 'Coupons')

@section('content')
<div x-data="{
    selectAll: false,
    selectedIds: [],
    toggleAll() {
        this.selectedIds = this.selectAll ? {{ $coupons->pluck('id') }} : [];
    }
}">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900">Coupons</h1>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                {{ $coupons->total() }}
            </span>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Create Coupon
        </a>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.coupons.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Code or description…" class="block w-full pl-9 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm">
                </div>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    <option value="expired" @selected(request('status') === 'expired')>Expired</option>
                </select>
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="type" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm">
                    <option value="">All Types</option>
                    <option value="fixed" @selected(request('type') === 'fixed')>Fixed Amount</option>
                    <option value="percentage" @selected(request('type') === 'percentage')>Percentage</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                    <svg class="inline h-4 w-4 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-4 py-2 rounded-lg transition-colors text-sm">Clear</a>
            </div>
        </div>
    </form>

    {{-- Floating Bulk Action Bar --}}
    <div x-show="selectedIds.length > 0" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40">
        <form method="POST" action="{{ route('admin.coupons.bulk-action') }}" class="flex items-center gap-3 bg-gray-900 text-white rounded-xl shadow-2xl px-5 py-3">
            @csrf
            <template x-for="id in selectedIds" :key="id">
                <input type="hidden" name="coupon_ids[]" :value="id">
            </template>
            <span class="text-sm font-medium" x-text="selectedIds.length + ' selected'"></span>
            <div class="w-px h-5 bg-gray-600"></div>
            <select name="action" required class="bg-gray-800 border-gray-600 text-white rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500 py-1.5">
                <option value="">Choose action…</option>
                <option value="activate">Activate</option>
                <option value="deactivate">Deactivate</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" onclick="return confirm('Are you sure you want to perform this bulk action?')" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-1.5 rounded-lg transition-colors text-sm">
                Apply
            </button>
            <button type="button" @click="selectedIds = []; selectAll = false" class="text-gray-400 hover:text-white transition-colors ml-1">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </form>
    </div>

    {{-- Coupons Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Min Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usage</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-amber-50/40 transition-colors">
                            <td class="px-4 py-3.5">
                                <input type="checkbox" value="{{ $coupon->id }}" x-model.number="selectedIds" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                            </td>
                            <td class="px-4 py-3.5">
                                <a href="{{ route('admin.coupons.show', $coupon) }}" class="group inline-flex items-center gap-2">
                                    <span class="font-mono text-sm font-semibold text-gray-900 bg-gray-100 border border-dashed border-gray-300 px-2.5 py-1 rounded-md group-hover:border-amber-400 group-hover:bg-amber-50 transition-colors tracking-wide">{{ $coupon->code }}</span>
                                </a>
                                @if($coupon->description)
                                    <p class="text-xs text-gray-400 mt-0.5 max-w-[180px] truncate">{{ $coupon->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($coupon->type === 'percentage')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M6.5 3a3.5 3.5 0 100 7 3.5 3.5 0 000-7zM5 6.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm8.5 3.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7zM12 13.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM15.354 3.354a.5.5 0 00-.708-.708l-11 11a.5.5 0 00.708.708l11-11z"/></svg>
                                        Percentage
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10.75 10.818a4.5 4.5 0 00-3.024-4.574.75.75 0 00-.462 1.427 3 3 0 012.015 3.147.75.75 0 001.471 0zM10 4.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2.5 10a7.5 7.5 0 1115 0 7.5 7.5 0 01-15 0z"/></svg>
                                        Fixed
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-semibold text-gray-900">
                                    @if($coupon->type === 'percentage')
                                        {{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}%
                                    @else
                                        ₱{{ number_format($coupon->value, 2) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-500">
                                {{ $coupon->minimum_amount ? '₱' . number_format($coupon->minimum_amount, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $coupon->usage_count ?? 0 }}</span>
                                    @if($coupon->usage_limit)
                                        <span class="text-xs text-gray-400">/ {{ $coupon->usage_limit }}</span>
                                        @php $usagePct = min(100, (($coupon->usage_count ?? 0) / $coupon->usage_limit) * 100); @endphp
                                        <div class="w-12 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $usagePct >= 90 ? 'bg-red-500' : ($usagePct >= 60 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $usagePct }}%"></div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">/ ∞</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @php
                                    $statusConfig = [
                                        'active'    => ['bg-emerald-50 text-emerald-700 ring-emerald-600/20', '●'],
                                        'inactive'  => ['bg-gray-50 text-gray-600 ring-gray-500/20', '●'],
                                        'expired'   => ['bg-red-50 text-red-700 ring-red-600/20', '●'],
                                        'scheduled' => ['bg-blue-50 text-blue-700 ring-blue-600/20', '◷'],
                                        'used_up'   => ['bg-amber-50 text-amber-700 ring-amber-600/20', '●'],
                                    ];
                                    $cfg = $statusConfig[$coupon->status] ?? ['bg-gray-50 text-gray-600 ring-gray-500/20', '●'];
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $cfg[0] }} ring-1 ring-inset">
                                    <span class="text-[8px]">{{ $cfg[1] }}</span>
                                    {{ ucfirst(str_replace('_', ' ', $coupon->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-xs text-gray-500 space-y-0.5">
                                    <div class="flex items-center gap-1">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $coupon->starts_at ? $coupon->starts_at->format('M d, Y') : 'No start' }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : 'Never' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.coupons.show', $coupon) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="View">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="h-16 w-16 rounded-full bg-amber-50 flex items-center justify-center mb-4">
                                        <svg class="h-8 w-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">No coupons found</p>
                                    <p class="text-sm text-gray-500 mt-1">Get started by creating your first discount coupon.</p>
                                    <a href="{{ route('admin.coupons.create') }}" class="mt-4 inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        Create Coupon
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50/50">
                {{ $coupons->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
