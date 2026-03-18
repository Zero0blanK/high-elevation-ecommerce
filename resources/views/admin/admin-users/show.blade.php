@extends('admin.layouts.app')

@section('title', $Admin->name . ' — Admin User')

@section('content')
@php
    $roleStyles = [
        'super_admin' => 'bg-red-50 text-red-700 ring-red-600/20',
        'admin'       => 'bg-purple-50 text-purple-700 ring-purple-600/20',
        'manager'     => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'staff'       => 'bg-green-50 text-green-700 ring-green-600/20',
    ];
    $isSelf = auth()->guard('admin')->id() === $Admin->id;
    $isLastSuperAdmin = $Admin->role === 'super_admin' && \App\Models\Admin::where('role', 'super_admin')->count() <= 1;
    $canDelete = !$isSelf && !$isLastSuperAdmin;
@endphp

<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <a href="{{ route('admin.admin-users.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-amber-600 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Admin Users
        </a>
    </div>

    {{-- Profile Header Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-24 sm:h-32"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between -mt-12 sm:-mt-16 gap-4">
                <div class="flex items-end gap-4">
                    <img class="h-24 w-24 sm:h-28 sm:w-28 rounded-xl object-cover ring-4 ring-white shadow-lg"
                         src="https://ui-avatars.com/api/?name={{ urlencode($Admin->name) }}&background=d97706&color=fff&size=224&font-size=0.35&bold=true"
                         alt="{{ $Admin->name }}">
                    <div class="pb-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $Admin->name }}</h1>
                            @if($isSelf)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-100 text-amber-700">You</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $Admin->email }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $roleStyles[$Admin->role] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20' }}">
                                {{ ucwords(str_replace('_', ' ', $Admin->role)) }}
                            </span>
                            @if($Admin->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 ring-1 ring-inset ring-gray-300/50">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 sm:pb-1">
                    <a href="{{ route('admin.admin-users.edit', $Admin) }}"
                       class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-amber-600 rounded-lg hover:bg-amber-700 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>

                    @if($canDelete)
                        <div x-data="{ confirmDelete: false }">
                            <button @click="confirmDelete = true"
                                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>

                            {{-- Delete Confirmation Modal --}}
                            <template x-teleport="body">
                                <div x-show="confirmDelete" x-cloak
                                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                     x-transition:enter="ease-out duration-200" x-transition:leave="ease-in duration-150">
                                    <div class="fixed inset-0 bg-gray-900/50" @click="confirmDelete = false"></div>
                                    <div class="relative bg-white rounded-xl shadow-xl max-w-sm w-full p-6"
                                         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150"
                                         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                        <div class="flex items-center justify-center w-12 h-12 mx-auto rounded-full bg-red-100 mb-4">
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 text-center">Delete Admin User</h3>
                                        <p class="mt-2 text-sm text-gray-500 text-center">
                                            Are you sure you want to delete <span class="font-medium text-gray-700">{{ $Admin->name }}</span>? This action cannot be undone.
                                        </p>
                                        <div class="mt-6 flex gap-3">
                                            <button @click="confirmDelete = false"
                                                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                                Cancel
                                            </button>
                                            <form action="{{ route('admin.admin-users.destroy', $Admin) }}" method="POST" class="flex-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-full px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    @else
                        <button disabled
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-300 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed"
                                title="{{ $isSelf ? 'Cannot delete your own account' : 'Cannot delete the last super admin' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Details Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Profile Information</h2>
        </div>
        <dl class="divide-y divide-gray-100">
            <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Full Name
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 font-medium">{{ $Admin->name }}</dd>
            </div>
            <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Email Address
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $Admin->email }}</dd>
            </div>
            <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Role
                </dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $roleStyles[$Admin->role] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20' }}">
                        {{ ucwords(str_replace('_', ' ', $Admin->role)) }}
                    </span>
                </dd>
            </div>
            <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Status
                </dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                    @if($Admin->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">
                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                            Inactive
                        </span>
                    @endif
                </dd>
            </div>
            <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Member Since
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                    {{ $Admin->created_at->format('F d, Y') }}
                    <span class="text-gray-400 ml-1">({{ $Admin->created_at->diffForHumans() }})</span>
                </dd>
            </div>
            <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Last Login
                </dt>
                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                    @if($Admin->last_login_at)
                        <span class="text-gray-900">{{ $Admin->last_login_at->format('F d, Y \a\t h:i A') }}</span>
                        <span class="text-gray-400 ml-1">({{ $Admin->last_login_at->diffForHumans() }})</span>
                    @else
                        <span class="text-gray-400 italic">Never logged in</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>
</div>
@endsection
