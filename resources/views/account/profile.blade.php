@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
        <p class="text-gray-600 mt-2">Manage your personal information</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-6 p-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" 
                           name="first_name" 
                           id="first_name" 
                           value="{{ old('first_name', $customer->first_name) }}"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" 
                           name="last_name" 
                           id="last_name" 
                           value="{{ old('last_name', $customer->last_name) }}"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email', $customer->email) }}"
                       class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" 
                           name="phone" 
                           id="phone" 
                           value="{{ old('phone', $customer->phone) }}"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" 
                           name="date_of_birth" 
                           id="date_of_birth" 
                           value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-md font-medium">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Section -->
    <div class="bg-white shadow rounded-lg mt-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
        </div>
        
        <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-6 p-6">
            @csrf
            @method('PATCH')
            <input type="hidden" name="update_password" value="1">

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" 
                       name="current_password" 
                       id="current_password"
                       class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation"
                           class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-md font-medium">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection