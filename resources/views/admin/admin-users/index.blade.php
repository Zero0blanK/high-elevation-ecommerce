@extends('admin.layouts.app')

@section('title', 'Admin Users')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Admin Users</h1>
            <p class="mt-1 text-sm text-gray-500">Manage administrator accounts and permissions.</p>
        </div>
        <a href="{{ route('admin.admin-users.create') }}"
           class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-semibold rounded-lg text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 shadow-sm transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Admin
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $total = $Admins->count();
            $active = $Admins->where('is_active', true)->count();
            $inactive = $total - $active;
            $superAdmins = $Admins->where('role', 'super_admin')->count();
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Active</p>
            <p class="mt-1 text-2xl font-bold text-green-600">{{ $active }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Inactive</p>
            <p class="mt-1 text-2xl font-bold text-red-600">{{ $inactive }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Super Admins</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $superAdmins }}</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Active</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($Admins as $admin)
                        @php
                            $roleStyles = [
                                'super_admin' => 'bg-red-50 text-red-700 ring-red-600/20',
                                'admin'       => 'bg-purple-50 text-purple-700 ring-purple-600/20',
                                'manager'     => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                'staff'       => 'bg-green-50 text-green-700 ring-green-600/20',
                            ];
                            $isSelf = auth()->guard('admin')->id() === $admin->id;
                            $isLastSuperAdmin = $admin->role === 'super_admin' && $superAdmins <= 1;
                            $canDelete = !$isSelf && !$isLastSuperAdmin;
                        @endphp
                        <tr class="hover:bg-amber-50/30 transition-colors">
                            {{-- User --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white shadow-sm"
                                         src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=d97706&color=fff&size=80&font-size=0.35&bold=true"
                                         alt="{{ $admin->name }}">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $admin->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $admin->email }}</p>
                                    </div>
                                    @if($isSelf)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-700">You</span>
                                    @endif
                                </div>
                            </td>
                            {{-- Role --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $roleStyles[$admin->role] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20' }}">
                                    {{ ucwords(str_replace('_', ' ', $admin->role)) }}
                                </span>
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($admin->is_active)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700">
                                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400">
                                        <span class="h-2 w-2 rounded-full bg-gray-300"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            {{-- Last Active --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.admin-users.show', $admin) }}"
                                       class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition" title="View">
                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.admin-users.edit', $admin) }}"
                                       class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit">
                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($canDelete)
                                        <div x-data="{ confirmDelete: false }" class="relative">
                                            <button @click="confirmDelete = true"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
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
                                                            Are you sure you want to delete <span class="font-medium text-gray-700">{{ $admin->name }}</span>? This action cannot be undone.
                                                        </p>
                                                        <div class="mt-6 flex gap-3">
                                                            <button @click="confirmDelete = false"
                                                                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                                                Cancel
                                                            </button>
                                                            <form action="{{ route('admin.admin-users.destroy', $admin) }}" method="POST" class="flex-1">
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
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-200 cursor-not-allowed" title="{{ $isSelf ? 'Cannot delete yourself' : 'Cannot delete last super admin' }}">
                                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900">No admin users found</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new admin user.</p>
                                    <a href="{{ route('admin.admin-users.create') }}"
                                       class="mt-4 inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Admin
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($Admins, 'hasPages') && $Admins->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $Admins->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
