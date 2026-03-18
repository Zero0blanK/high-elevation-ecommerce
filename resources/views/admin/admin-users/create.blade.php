@extends('admin.layouts.app')

@section('title', 'Add Admin User')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('admin.admin-users.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-amber-600 transition mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Admin Users
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Add Admin User</h1>
            <p class="mt-1 text-sm text-gray-500">Create a new administrator account.</p>
        </div>
    </div>

    {{-- Validation Errors Summary --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-red-800">Please fix the following errors:</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Card --}}
    <form action="{{ route('admin.admin-users.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-6 space-y-6">

                {{-- Account Information --}}
                <div>
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Account Information</h2>
                    <div class="space-y-5">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Enter full name"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm @error('name') border-red-300 ring-1 ring-red-300 @enderror">
                            @error('name')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="admin@example.com"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm @error('email') border-red-300 ring-1 ring-red-300 @enderror">
                            @error('email')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                {{-- Password --}}
                <div>
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Password</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" id="password" required placeholder="••••••••"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm @error('password') border-red-300 ring-1 ring-red-300 @enderror">
                            @error('password')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="••••••••"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                {{-- Role & Status --}}
                <div>
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Role & Permissions</h2>
                    <div class="space-y-5">
                        {{-- Role --}}
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                            <select name="role" id="role" required x-data x-on:change="$dispatch('role-changed', $el.value)"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm @error('role') border-red-300 ring-1 ring-red-300 @enderror">
                                <option value="">Select a role...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $role)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            {{-- Role Descriptions --}}
                            <div class="mt-3 space-y-2" x-data="{ selected: '{{ old('role', '') }}' }" x-on:role-changed.window="selected = $event.detail">
                                <div x-show="selected === 'super_admin' || selected === ''" x-cloak class="flex items-start gap-2 text-xs p-2.5 rounded-lg bg-red-50 border border-red-100">
                                    <span class="w-2 h-2 rounded-full bg-red-500 mt-1 flex-shrink-0"></span>
                                    <span class="text-red-700"><strong>Super Admin</strong> — Full system access. Can manage all admins, settings, and configurations.</span>
                                </div>
                                <div x-show="selected === 'admin' || selected === ''" x-cloak class="flex items-start gap-2 text-xs p-2.5 rounded-lg bg-purple-50 border border-purple-100">
                                    <span class="w-2 h-2 rounded-full bg-purple-500 mt-1 flex-shrink-0"></span>
                                    <span class="text-purple-700"><strong>Admin</strong> — Manage products, orders, and customers. Cannot modify system settings.</span>
                                </div>
                                <div x-show="selected === 'manager' || selected === ''" x-cloak class="flex items-start gap-2 text-xs p-2.5 rounded-lg bg-blue-50 border border-blue-100">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 mt-1 flex-shrink-0"></span>
                                    <span class="text-blue-700"><strong>Manager</strong> — Manage products and view orders. Limited administrative access.</span>
                                </div>
                                <div x-show="selected === 'staff' || selected === ''" x-cloak class="flex items-start gap-2 text-xs p-2.5 rounded-lg bg-green-50 border border-green-100">
                                    <span class="w-2 h-2 rounded-full bg-green-500 mt-1 flex-shrink-0"></span>
                                    <span class="text-green-700"><strong>Staff</strong> — View-only access to orders and products. Cannot make changes.</span>
                                </div>
                            </div>
                        </div>

                        {{-- Active Toggle --}}
                        <div x-data="{ active: {{ old('is_active', '1') ? 'true' : 'false' }} }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Status</label>
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-gray-50/50">
                                <button type="button" @click="active = !active" role="switch" :aria-checked="active"
                                        :class="active ? 'bg-amber-600' : 'bg-gray-300'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                    <span :class="active ? 'translate-x-5' : 'translate-x-0'"
                                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                                <input type="hidden" name="is_active" :value="active ? 1 : 0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="active ? 'Active' : 'Inactive'"></p>
                                    <p class="text-xs text-gray-500" x-text="active ? 'User can log in and access the admin panel.' : 'User is blocked from logging in.'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <a href="{{ route('admin.admin-users.index') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-amber-600 rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Admin
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
