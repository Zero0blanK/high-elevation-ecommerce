@extends('admin.layouts.app')

@section('title', 'Edit Customer — ' . $customer->first_name . ' ' . $customer->last_name)

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Customer</h1>
            <p class="mt-1 text-sm text-gray-500">Update information for {{ $customer->first_name }} {{ $customer->last_name }}</p>
        </div>
        <a href="{{ route('admin.customers.show', $customer) }}"
           class="inline-flex items-center px-3.5 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Customer
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column — Form --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                @csrf
                @method('PATCH')

                {{-- Personal Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-5">Personal Information</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $customer->first_name) }}" required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm @error('first_name') border-red-300 ring-red-300 @enderror">
                            @error('first_name')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $customer->last_name) }}" required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm @error('last_name') border-red-300 ring-red-300 @enderror">
                            @error('last_name')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" required
                                       class="block w-full pl-9 rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm @error('email') border-red-300 ring-red-300 @enderror">
                            </div>
                            @error('email')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}"
                                       class="block w-full pl-9 rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm @error('phone') border-red-300 ring-red-300 @enderror">
                            </div>
                            @error('phone')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-600 focus:ring-amber-600 sm:text-sm @error('date_of_birth') border-red-300 ring-red-300 @enderror">
                            @error('date_of_birth')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Account Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Account Status</h2>
                    <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 border border-gray-200">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Active Account</p>
                            <p class="text-xs text-gray-500 mt-0.5">Inactive customers cannot log in or place orders.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-600/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                        </label>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.customers.show', $customer) }}"
                       class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-600 shadow-sm transition-colors">
                        <svg class="inline -ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Column — Summary --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Customer Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="h-12 w-12 rounded-full bg-amber-100 flex items-center justify-center">
                        <span class="text-lg font-bold text-amber-700">{{ strtoupper(substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                    </div>
                </div>
                <dl class="space-y-3">
                    <div class="flex justify-between items-center py-2.5 border-b border-gray-100">
                        <dt class="text-sm text-gray-500 flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Joined
                        </dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $customer->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between items-center py-2.5 border-b border-gray-100">
                        <dt class="text-sm text-gray-500 flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Total Orders
                        </dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $customer->orders_count ?? $customer->orders->count() ?? 0 }}</dd>
                    </div>
                    <div class="flex justify-between items-center py-2.5 border-b border-gray-100">
                        <dt class="text-sm text-gray-500 flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Last Order
                        </dt>
                        <dd class="text-sm font-medium text-gray-900">
                            @if($customer->orders && $customer->orders->count() > 0)
                                {{ $customer->orders->first()->created_at->format('M d, Y') }}
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between items-center py-2.5">
                        <dt class="text-sm text-gray-500 flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Status
                        </dt>
                        <dd>
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
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Addresses (read-only) --}}
            @if($customer->addresses && $customer->addresses->count())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Saved Addresses</h2>
                    <div class="space-y-3">
                        @foreach($customer->addresses as $address)
                            <div class="rounded-lg border border-gray-200 p-3 text-sm">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $address->type === 'shipping' ? 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10' : 'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-700/10' }}">
                                        {{ ucfirst($address->type) }}
                                    </span>
                                    @if($address->is_default)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-100 text-amber-800">Default</span>
                                    @endif
                                </div>
                                <p class="font-medium text-gray-900">{{ $address->first_name }} {{ $address->last_name }}</p>
                                <p class="text-gray-500">{{ $address->address_line_1 }}</p>
                                @if($address->address_line_2)
                                    <p class="text-gray-500">{{ $address->address_line_2 }}</p>
                                @endif
                                <p class="text-gray-500">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                <p class="text-gray-500">{{ $address->country }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Coffee Preferences (read-only) --}}
            @if($customer->preferences)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Coffee Preferences</h2>
                    <dl class="space-y-2 text-sm">
                        @if($customer->preferences->preferred_roast)
                            <div class="flex justify-between py-1.5">
                                <dt class="text-gray-500">Roast</dt>
                                <dd class="font-medium text-gray-900">{{ ucfirst($customer->preferences->preferred_roast) }}</dd>
                            </div>
                        @endif
                        @if($customer->preferences->preferred_grind)
                            <div class="flex justify-between py-1.5">
                                <dt class="text-gray-500">Grind</dt>
                                <dd class="font-medium text-gray-900">{{ ucfirst($customer->preferences->preferred_grind) }}</dd>
                            </div>
                        @endif
                        @if($customer->preferences->brewing_method)
                            <div class="flex justify-between py-1.5">
                                <dt class="text-gray-500">Brewing</dt>
                                <dd class="font-medium text-gray-900">{{ ucfirst($customer->preferences->brewing_method) }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
