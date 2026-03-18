@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <p class="mt-1 text-sm text-gray-500">View and manage your account information.</p>
    </div>

    {{-- Success Messages --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <svg class="h-5 w-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Avatar Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-br from-amber-500 to-amber-700 h-28"></div>
                <div class="px-6 pb-6 text-center -mt-14">
                    <img class="h-28 w-28 rounded-full ring-4 ring-white mx-auto object-cover shadow-md"
                         src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=d97706&color=fff&size=112&font-size=0.4&bold=true"
                         alt="{{ $admin->name }}">
                    <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $admin->name }}</h2>
                    @php
                        $roleColors = [
                            'super_admin' => 'bg-purple-100 text-purple-800',
                            'admin' => 'bg-blue-100 text-blue-800',
                            'manager' => 'bg-amber-100 text-amber-800',
                            'staff' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $roleColors[$admin->role] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $admin->role_display }}
                    </span>
                    <p class="mt-2 text-sm text-gray-500">{{ $admin->email }}</p>
                </div>

                {{-- Quick Actions --}}
                <div class="border-t border-gray-200 px-6 py-4 space-y-3">
                    <a href="{{ route('admin.profile.edit') }}"
                       class="w-full flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Profile
                    </a>
                    <a href="{{ route('admin.profile.edit') }}#change-password"
                       class="w-full flex items-center justify-center gap-2 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Change Password
                    </a>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Account Details</h3>
                </div>
                <dl class="divide-y divide-gray-100">
                    <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 items-center">
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 sm:col-span-2 sm:mt-0">{{ $admin->name }}</dd>
                    </div>
                    <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 items-center">
                        <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $admin->email }}</dd>
                    </div>
                    <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 items-center">
                        <dt class="text-sm font-medium text-gray-500">Role</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $admin->role_display }}</dd>
                    </div>
                    <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 items-center">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            @if($admin->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Inactive
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 items-center">
                        <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $admin->created_at->format('F d, Y') }}</dd>
                    </div>
                    <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 items-center">
                        <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $admin->last_login_at ? $admin->last_login_at->format('M d, Y h:i A') : 'Never' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection