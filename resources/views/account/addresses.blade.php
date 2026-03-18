
@extends('layouts.app')

@section('title', 'My Addresses')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    showAddModal: false,
    showEditModal: false,
    editForm: { id: '', type: 'shipping', first_name: '', last_name: '', company: '', address_line_1: '', address_line_2: '', city: '', state: '', postal_code: '', country: 'United States', is_default: false },

    openEdit(id) {
        fetch(`{{ route('account.addresses.show') }}?address_id=${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message);
            const a = data.address;
            this.editForm = { id: a.id, type: a.type||'', first_name: a.first_name||'', last_name: a.last_name||'', company: a.company||'', address_line_1: a.address_line_1||'', address_line_2: a.address_line_2||'', city: a.city||'', state: a.state||'', postal_code: a.postal_code||'', country: a.country||'United States', is_default: a.is_default||false };
            this.showEditModal = true;
        })
        .catch(() => alert('Failed to load address details.'));
    },

    submitEdit() {
        const data = new URLSearchParams(this.editForm);
        data.append('_method', 'PATCH');
        data.append('_token', document.querySelector('meta[name=\'csrf-token\']').content);
        data.set('is_default', this.editForm.is_default ? '1' : '0');
        fetch(`{{ url('account/addresses') }}/${this.editForm.id}`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data
        })
        .then(r => { if (!r.ok) throw new Error(); window.location.reload(); })
        .catch(() => alert('Failed to update address.'));
    }
}">
    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <!-- Sidebar (desktop) -->
        <div class="hidden lg:block lg:col-span-3">
            @include('account.partials.sidebar')
        </div>

        <div class="lg:col-span-9">
            <!-- Mobile tab navigation -->
            <div class="lg:hidden mb-6 flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <a href="{{ route('account.dashboard') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">Dashboard</a>
                <a href="{{ route('orders.index') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">Orders</a>
                <a href="{{ route('wishlist.index') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">Wishlist</a>
                <a href="{{ route('account.addresses') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-amber-600 text-white">Addresses</a>
                <a href="{{ route('account.profile') }}" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">Profile</a>
            </div>

            <!-- Page Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Addresses</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your shipping and billing addresses</p>
                </div>
                <button @click="showAddModal = true" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    + Add Address
                </button>
            </div>

            <!-- Addresses Grid -->
            @if($addresses->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($addresses as $address)
                        <div class="bg-white rounded-xl shadow-sm border {{ $address->is_default ? 'border-amber-400 ring-1 ring-amber-200' : 'border-gray-200' }} overflow-hidden">
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $address->full_name }}</h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($address->type === 'billing') bg-blue-50 text-blue-700
                                                @elseif($address->type === 'shipping') bg-green-50 text-green-700
                                                @else bg-purple-50 text-purple-700
                                                @endif">
                                                {{ ucfirst($address->type) }}
                                            </span>
                                            @if($address->is_default)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700">Default</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button @click="openEdit({{ $address->id }})" class="p-1.5 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-gray-100 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" class="inline"
                                              onsubmit="return confirm('Delete this address?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100 transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600 space-y-0.5">
                                    @if($address->company)<p class="font-medium">{{ $address->company }}</p>@endif
                                    <p>{{ $address->address_line_1 }}</p>
                                    @if($address->address_line_2)<p>{{ $address->address_line_2 }}</p>@endif
                                    <p>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                    <p>{{ $address->country }}</p>
                                    @if($address->phone)<p class="text-gray-500 mt-1">{{ $address->phone }}</p>@endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <h3 class="mt-3 text-sm font-medium text-gray-900">No addresses yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Add your first address to get started.</p>
                    <div class="mt-4">
                        <button @click="showAddModal = true" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Add Address
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Address Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 @click="showAddModal = false" class="fixed inset-0 bg-gray-900/50"></div>
            <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6 z-10">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Add New Address</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('account.addresses.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                        <select name="type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="shipping">Shipping</option>
                            <option value="billing">Billing</option>
                            <option value="both">Both</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="first_name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="last_name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-gray-400">(optional)</span></label>
                        <input type="text" name="company" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                        <input type="text" name="address_line_1" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 <span class="text-gray-400">(optional)</span></label>
                        <input type="text" name="address_line_2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="city" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <input type="text" name="state" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" name="postal_code" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <select name="country" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="United States">United States</option>
                                <option value="Canada">Canada</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Philippines">Philippines</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-gray-400">(optional)</span></label>
                            <input type="tel" name="phone" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>
                    <input type="hidden" name="is_default" value="0">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_default" value="1" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                        <span class="text-sm text-gray-700">Set as default address</span>
                    </label>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">Add Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Address Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 @click="showEditModal = false" class="fixed inset-0 bg-gray-900/50"></div>
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6 z-10">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Address</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form @submit.prevent="submitEdit()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                        <select x-model="editForm.type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="shipping">Shipping</option>
                            <option value="billing">Billing</option>
                            <option value="both">Both</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" x-model="editForm.first_name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" x-model="editForm.last_name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-gray-400">(optional)</span></label>
                        <input type="text" x-model="editForm.company" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                        <input type="text" x-model="editForm.address_line_1" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 <span class="text-gray-400">(optional)</span></label>
                        <input type="text" x-model="editForm.address_line_2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" x-model="editForm.city" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <input type="text" x-model="editForm.state" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" x-model="editForm.postal_code" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <select x-model="editForm.country" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="United States">United States</option>
                            <option value="Canada">Canada</option>
                            <option value="United Kingdom">United Kingdom</option>
                            <option value="Philippines">Philippines</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" x-model="editForm.is_default" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                        <span class="text-sm text-gray-700">Set as default address</span>
                    </label>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">Update Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
