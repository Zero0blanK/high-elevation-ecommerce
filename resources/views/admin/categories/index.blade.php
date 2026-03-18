@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
            <p class="mt-1 text-sm text-gray-500">Organize your products into categories and subcategories.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Category
        </a>
    </div>

    @if($categories->count())
        {{-- Category Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <div x-data="{ showDelete: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    {{-- Card Header --}}
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg overflow-hidden bg-amber-50 flex items-center justify-center">
                                    @if($category->image_url)
                                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $category->name }}</h3>
                                    <p class="text-xs text-gray-400">{{ $category->slug }}</p>
                                </div>
                            </div>
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">Inactive</span>
                            @endif
                        </div>

                        @if($category->description)
                            <p class="mt-3 text-sm text-gray-600 line-clamp-2">{{ $category->description }}</p>
                        @endif

                        {{-- Stats --}}
                        <div class="mt-4 flex items-center gap-4">
                            <div class="flex items-center gap-1.5 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span class="font-medium text-gray-700">{{ $category->products_count }}</span> {{ Str::plural('product', $category->products_count) }}
                            </div>
                            <div class="flex items-center gap-1.5 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                <span class="font-medium text-gray-700">{{ $category->children_count }}</span> {{ Str::plural('subcategory', $category->children_count) }}
                            </div>
                        </div>

                        {{-- Subcategories --}}
                        @if($category->children->count())
                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Subcategories</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($category->children->take(5) as $child)
                                        <a href="{{ route('admin.categories.show', $child) }}"
                                           class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-600 hover:bg-gray-100 transition-colors">
                                            {{ $child->name }}
                                            <span class="ml-1 text-gray-400">({{ $child->products_count }})</span>
                                        </a>
                                    @endforeach
                                    @if($category->children->count() > 5)
                                        <span class="inline-flex items-center px-2 py-1 text-xs text-gray-400">+{{ $category->children->count() - 5 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Card Actions --}}
                    <div class="border-t border-gray-100 bg-gray-50/50 px-5 py-3 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.categories.show', $category) }}"
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 bg-white rounded-md border border-gray-200 hover:border-gray-300 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                        <a href="{{ route('admin.categories.edit', $category) }}"
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-amber-700 hover:text-amber-800 bg-amber-50 rounded-md border border-amber-200 hover:border-amber-300 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        <button @click="showDelete = true"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-700 bg-red-50 rounded-md border border-red-200 hover:border-red-300 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </div>

                    {{-- Delete Confirmation Modal --}}
                    <template x-teleport="body">
                        <div x-show="showDelete" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
                            <div class="flex min-h-full items-center justify-center p-4">
                                <div x-show="showDelete" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                     @click="showDelete = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
                                <div x-show="showDelete" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                     class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Delete Category</h3>
                                            <p class="mt-1 text-sm text-gray-500">Are you sure you want to delete <strong>{{ $category->name }}</strong>? This action cannot be undone.</p>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button @click="showDelete = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                            Cancel
                                        </button>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 py-16 px-6">
            <div class="text-center max-w-sm mx-auto">
                <div class="mx-auto w-16 h-16 rounded-full bg-amber-50 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">No categories yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first category to organize your products.</p>
                <a href="{{ route('admin.categories.create') }}"
                   class="mt-6 inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Category
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
