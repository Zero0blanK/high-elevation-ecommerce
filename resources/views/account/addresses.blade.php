
@extends('layouts.app')

@section('title', 'My Addresses')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Addresses</h1>
            <p class="text-gray-600 mt-2">Manage your shipping and billing addresses</p>
        </div>
        <button onclick="openAddAddressModal()" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md font-medium">
            Add New Address
        </button>
    </div>

    @if($addresses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($addresses as $address)
                <div class="bg-white rounded-lg shadow border {{ $address->is_default ? 'border-amber-500' : 'border-gray-200' }}">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $address->full_name }}</h3>
                                @if($address->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Default
                                    </span>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="editAddress({{ $address->id }})" class="text-gray-400 hover:text-gray-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this address?')" class="text-gray-400 hover:text-red-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600 space-y-1">
                            @if($address->company)
                                <p class="font-medium">{{ $address->company }}</p>
                            @endif
                            <p>{{ $address->address_line_1 }}</p>
                            @if($address->address_line_2)
                                <p>{{ $address->address_line_2 }}</p>
                            @endif
                            <p>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                            <p>{{ $address->country }}</p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($address->type === 'billing') bg-blue-100 text-blue-800
                                @elseif($address->type === 'shipping') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                {{ ucfirst($address->type) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No addresses</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding your first address.</p>
            <div class="mt-6">
                <button onclick="openAddAddressModal()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                    Add Address
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Add Address Modal -->
<div id="addAddressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Address</h3>
                <button onclick="closeAddAddressModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('account.addresses.store') }}" class="space-y-4">
                @csrf
                
                <!-- Address Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Address Type</label>
                    <select name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                        <option value="shipping">Shipping</option>
                        <option value="billing">Billing</option>
                        <option value="both">Both</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- Company -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company (Optional)</label>
                    <input type="text" name="company" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Address Line 1 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Address Line 1</label>
                    <input type="text" name="address_line_1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Address Line 2 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                    <input type="text" name="address_line_2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <!-- State -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">State</label>
                        <input type="text" name="state" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <input type="text" name="postal_code" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- Country -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Country</label>
                    <select name="country" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                        <option value="United States">United States</option>
                        <option value="Canada">Canada</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <!-- Add more countries as needed -->
                    </select>
                </div>

                <!-- Default Address -->
                <input type="hidden" name="is_default" value="0">
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="is_default" class="ml-2 block text-sm text-gray-900">Set as default address</label>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddAddressModal()" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                        Add Address
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function openAddAddressModal() {
        document.getElementById('addAddressModal').classList.remove('hidden');
    }

    function closeAddAddressModal() {
        document.getElementById('addAddressModal').classList.add('hidden');
    }

    function editAddress(id) {
        // Set form action using Laravel route
        const form = document.getElementById('updateAddressForm');
        form.action = "{{ url('account/addresses') }}/" + id;
        
        // Make an AJAX request to get address details
        fetch(`{{ route('account.addresses.show') }}?address_id=${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to load address');
            }
            const address = data.address;
            // Debug output
            console.log('Received address data:', address);
            
            // Populate form fields
            form.querySelector('[name="type"]').value = address.type || '';
            form.querySelector('[name="first_name"]').value = address.first_name || '';
            form.querySelector('[name="last_name"]').value = address.last_name || '';
            form.querySelector('[name="company"]').value = address.company || '';
            form.querySelector('[name="address_line_1"]').value = address.address_line_1 || '';
            form.querySelector('[name="address_line_2"]').value = address.address_line_2 || '';
            form.querySelector('[name="city"]').value = address.city || '';
            form.querySelector('[name="state"]').value = address.state || '';
            form.querySelector('[name="postal_code"]').value = address.postal_code || '';
            form.querySelector('[name="country"]').value = address.country || '';
            form.querySelector('#edit_is_default').checked = address.is_default || false;
                
                // Show the modal
                document.getElementById('editAddressModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching address:', error);
                alert('Failed to load address details. Please try again.');
            });
    }

    function closeEditAddressModal() {
        document.getElementById('editAddressModal').classList.add('hidden');
    }

    function handleAddressUpdate(event) {
        event.preventDefault();
        const form = event.target;
        const url = form.action;
        const formData = new FormData(form);

        // Convert FormData to URL-encoded string
        const data = new URLSearchParams(formData);
        data.append('_method', 'PATCH'); // Add the method spoofing

        fetch(url, {
            method: 'POST', // Always use POST for form submission
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: data
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            window.location.reload(); // Reload the page to show updated data
        })
        .catch(error => {
            console.error('Error updating address:', error);
            alert('Failed to update address. Please try again.');
        });
    }
</script>

<!-- Edit Address Modal -->
<div id="editAddressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Address</h3>
                <button onclick="closeEditAddressModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="updateAddressForm" method="POST" class="space-y-4" onsubmit="handleAddressUpdate(event)">
                @csrf
                @method('PATCH')
                
                <!-- Address Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Address Type</label>
                    <select name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                        <option value="shipping">Shipping</option>
                        <option value="billing">Billing</option>
                        <option value="both">Both</option>
                    </select>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- Company -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company (Optional)</label>
                    <input type="text" name="company" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Address Line 1 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Address Line 1</label>
                    <input type="text" name="address_line_1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Address Line 2 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                    <input type="text" name="address_line_2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <!-- State -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">State</label>
                        <input type="text" name="state" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <input type="text" name="postal_code" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- Country -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Country</label>
                    <select name="country" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                        <option value="United States">United States</option>
                        <option value="Canada">Canada</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <!-- Add more countries as needed -->
                    </select>
                </div>

                <!-- Default Address -->
                <input type="hidden" name="is_default" value="0">
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="edit_is_default" value="1" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="edit_is_default" class="ml-2 block text-sm text-gray-900">Set as default address</label>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditAddressModal()" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                        Update Address
                    </button>
                </div>
            </form>
        </div>  
    </div>
</div>
@endsection
