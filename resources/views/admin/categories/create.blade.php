@extends('admin.layouts.app')

@section('title', 'Add Category')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.categories.index') }}"
           class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add Category</h1>
            <p class="text-sm text-gray-500">Create a new product category.</p>
        </div>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data"
          x-data="{
              name: '{{ old('name') }}',
              get slug() { return this.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, ''); },
              imagePreview: null,
              handleImage(event) {
                  const file = event.target.files[0];
                  if (file) {
                      const reader = new FileReader();
                      reader.onload = (e) => this.imagePreview = e.target.result;
                      reader.readAsDataURL(file);
                  }
              }
          }">
        @csrf

        {{-- Basic Information --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-900">Basic Information</h2>

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" x-model="name" required
                       class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('name') border-red-300 @enderror"
                       placeholder="e.g. Single Origin Beans">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                <input type="text" name="slug" id="slug" :value="slug" readonly
                       class="mt-1 block w-full border-gray-300 rounded-lg bg-gray-50 shadow-sm sm:text-sm text-gray-500 cursor-not-allowed">
                <p class="mt-1 text-xs text-gray-400">Auto-generated from the category name.</p>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('description') border-red-300 @enderror"
                          placeholder="Brief description of this category...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Parent Category --}}
            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Category</label>
                <select name="parent_id" id="parent_id"
                        class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('parent_id') border-red-300 @enderror">
                    <option value="">None — Top Level</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort Order & Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('sort_order') border-red-300 @enderror">
                    @error('sort_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                        <span class="ml-3 text-sm text-gray-600">Active</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Image Upload --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-900">Category Image</h2>
            <div class="flex items-start gap-5">
                <div class="flex-shrink-0 w-24 h-24 rounded-lg border-2 border-dashed border-gray-300 overflow-hidden flex items-center justify-center bg-gray-50">
                    <template x-if="imagePreview">
                        <img :src="imagePreview" class="w-full h-full object-cover" alt="Preview">
                    </template>
                    <template x-if="!imagePreview">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </template>
                </div>
                <div class="flex-1">
                    <input type="file" name="image" id="image" accept="image/*" @change="handleImage($event)"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                    <p class="mt-1 text-xs text-gray-400">PNG, JPG or WebP. Recommended size 400×400px.</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- SEO Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">SEO Settings</h2>
                    <p class="text-sm text-gray-500">Optimize for search engines.</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-transition.duration.200ms class="space-y-4 pt-2">
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" maxlength="255"
                           class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('meta_title') border-red-300 @enderror"
                           placeholder="SEO-friendly title for this category">
                    @error('meta_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="2" maxlength="500"
                              class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm sm:text-sm @error('meta_description') border-red-300 @enderror"
                              placeholder="Brief description for search engine results...">{{ old('meta_description') }}</textarea>
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('admin.categories.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                Create Category
            </button>
        </div>
    </form>
</div>
@endsection
