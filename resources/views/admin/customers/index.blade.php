@extends('admin.layouts.app')

@section('title', 'Customers')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8" x-data="{
    showEmailModal: false,
    selectedIds: [],
    toggleAll: false,
    toggleAllCheckboxes() {
        this.toggleAll = !this.toggleAll;
        if (this.toggleAll) {
            this.selectedIds = [{{ $customers->pluck('id')->implode(',') }}];
        } else {
            this.selectedIds = [];
        }
    }
}">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $customers->total() }} {{ Str::plural('customer', $customers->total()) }} total</p>
        </div>
        <button @click="showEmailModal = true"
                class="inline-flex items-center px-4 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-600 transition-colors">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Send Email
        </button>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.customers.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, email, phone…"
                           class="block w-full pl-9 rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm">
                </div>
            </div>
            <div>
                <label for="is_active" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Status</label>
                <select name="is_active" id="is_active" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="1" @selected(request('is_active') === '1')>Active</option>
                    <option value="0" @selected(request('is_active') === '0')>Inactive</option>
                </select>
            </div>
            <div>
                <label for="has_orders" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Orders</label>
                <select name="has_orders" id="has_orders" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm">
                    <option value="">All Customers</option>
                    <option value="yes" @selected(request('has_orders') === 'yes')>With Orders</option>
                    <option value="no" @selected(request('has_orders') === 'no')>Without Orders</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-600 transition-colors">
                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">Clear</a>
            </div>
        </div>
    </form>

    {{-- Selected count indicator --}}
    <div x-show="selectedIds.length > 0" x-cloak class="mb-4 flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2.5">
        <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-medium text-amber-800" x-text="selectedIds.length + ' customer(s) selected'"></span>
        <button @click="showEmailModal = true" class="ml-auto text-sm font-medium text-amber-700 hover:text-amber-900 underline">Send Email to Selected</button>
    </div>

    {{-- Customers Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="w-12 px-4 py-3.5">
                            <input type="checkbox" @click="toggleAllCheckboxes()" :checked="toggleAll"
                                   class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-600">
                        </th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Phone</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Orders</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Spent</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Joined</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-amber-50/40 transition-colors">
                            <td class="px-4 py-3.5">
                                <input type="checkbox" value="{{ $customer->id }}" x-model.number="selectedIds"
                                       class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-600">
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 flex-shrink-0 rounded-full bg-amber-100 flex items-center justify-center">
                                        <span class="text-sm font-semibold text-amber-700">{{ strtoupper(substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1)) }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $customer->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-600">{{ $customer->phone ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-sm font-medium text-gray-900">{{ $customer->orders_count ?? 0 }}</td>
                            <td class="px-4 py-3.5 text-sm font-medium text-gray-900">₱{{ number_format($customer->total_spent ?? 0, 2) }}</td>
                            <td class="px-4 py-3.5">
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
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-500">{{ $customer->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.customers.show', $customer) }}" title="View"
                                       class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}" title="Edit"
                                       class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">No customers found</p>
                                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="px-4 py-3.5 border-t border-gray-200 bg-gray-50/50">
                {{ $customers->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Send Email Modal --}}
    <div x-show="showEmailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div x-show="showEmailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showEmailModal = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
            <div x-show="showEmailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full z-10">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Send Email to Customers</h3>
                            <p class="mt-1 text-sm text-gray-500" x-show="selectedIds.length > 0" x-text="selectedIds.length + ' customer(s) selected'"></p>
                        </div>
                        <button @click="showEmailModal = false" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.customers.send-email') }}">
                    @csrf
                    <div class="px-6 py-5 space-y-4">
                        {{-- Hidden inputs for selected customer IDs --}}
                        <template x-for="id in selectedIds" :key="id">
                            <input type="hidden" name="customer_ids[]" :value="id">
                        </template>

                        <div x-show="selectedIds.length === 0">
                            <label for="customer_ids_manual" class="block text-sm font-medium text-gray-700 mb-1">Customer IDs</label>
                            <input type="text" name="customer_ids_raw" id="customer_ids_manual" placeholder="e.g. 1, 2, 3 or select from table"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Comma-separated IDs, or select customers using checkboxes in the table.</p>
                        </div>
                        <div>
                            <label for="email_subject" class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
                            <input type="text" name="subject" id="email_subject" required placeholder="Enter email subject…"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm">
                        </div>
                        <div>
                            <label for="email_content" class="block text-sm font-medium text-gray-700 mb-1">Content <span class="text-red-500">*</span></label>
                            <textarea name="content" id="email_content" rows="5" required placeholder="Write your email content…"
                                      class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm"></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end gap-3">
                        <button type="button" @click="showEmailModal = false"
                                class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2.5 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-600 transition-colors">
                            <svg class="inline -ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
