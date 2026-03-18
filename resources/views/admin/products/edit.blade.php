@extends('admin.layouts.app')

@section('title', 'Edit: ' . $product->name)

@section('content')
<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.show', $product) }}" class="inline-flex items-center justify-center h-9 w-9 rounded-lg bg-white border border-gray-300 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
                <p class="text-sm text-gray-500">{{ $product->name }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">Basic Information</h2>
                    <div class="space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU <span class="text-red-500">*</span></label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" required class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('sku') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="5" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">{{ old('description', $product->description) }}</textarea>
                            @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Coffee Details --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">Coffee Details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="roast_level" class="block text-sm font-medium text-gray-700 mb-1">Roast Level</label>
                            <select name="roast_level" id="roast_level" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                <option value="">Select roast level</option>
                                <option value="light" @selected(old('roast_level', $product->roast_level) === 'light')>Light</option>
                                <option value="medium" @selected(old('roast_level', $product->roast_level) === 'medium')>Medium</option>
                                <option value="medium-dark" @selected(old('roast_level', $product->roast_level) === 'medium-dark')>Medium Dark</option>
                                <option value="dark" @selected(old('roast_level', $product->roast_level) === 'dark')>Dark</option>
                            </select>
                            @error('roast_level') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="grind_type" class="block text-sm font-medium text-gray-700 mb-1">Grind Type</label>
                            <select name="grind_type" id="grind_type" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                <option value="">Select grind type</option>
                                <option value="whole-bean" @selected(old('grind_type', $product->grind_type) === 'whole-bean')>Whole Bean</option>
                                <option value="coarse" @selected(old('grind_type', $product->grind_type) === 'coarse')>Coarse</option>
                                <option value="medium" @selected(old('grind_type', $product->grind_type) === 'medium')>Medium</option>
                                <option value="fine" @selected(old('grind_type', $product->grind_type) === 'fine')>Fine</option>
                                <option value="extra-fine" @selected(old('grind_type', $product->grind_type) === 'extra-fine')>Extra Fine</option>
                            </select>
                            @error('grind_type') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="origin" class="block text-sm font-medium text-gray-700 mb-1">Origin</label>
                            <input type="text" name="origin" id="origin" value="{{ old('origin', $product->origin) }}" placeholder="e.g. Ethiopia, Colombia" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('origin') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="flavor_notes" class="block text-sm font-medium text-gray-700 mb-1">Flavor Notes</label>
                            <textarea name="flavor_notes" id="flavor_notes" rows="2" placeholder="e.g. Chocolate, Citrus, Nutty" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">{{ old('flavor_notes', $product->flavor_notes) }}</textarea>
                            @error('flavor_notes') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Existing Images --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">Current Images</h2>
                    @if($product->images->count())
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                            @foreach($product->images as $image)
                                <div class="relative group aspect-square">
                                    <img src="{{ asset('storage/' . $image->image_url) }}" alt="{{ $image->alt_text ?? $product->name }}" class="h-full w-full rounded-xl object-cover">
                                    @if($image->is_primary)
                                        <span class="absolute top-2 left-2 bg-amber-600 text-white text-xs font-medium px-2 py-0.5 rounded-md shadow-sm">Primary</span>
                                    @endif
                                    <form method="POST" action="{{ route('admin.products.delete-image', [$product, $image]) }}" class="absolute top-2 right-2" onsubmit="return confirm('Delete this image?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-7 w-7 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded-full shadow-sm opacity-0 group-hover:opacity-100 transition-all" title="Delete image">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 bg-gray-50 rounded-xl mb-6">
                            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="mt-2 text-sm text-gray-500">No images uploaded yet</p>
                        </div>
                    @endif

                    {{-- Upload New Images --}}
                    <div x-data="{ previews: [], dragging: false }">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Add More Images</h3>
                        <label class="relative flex flex-col items-center justify-center px-6 py-6 border-2 border-dashed rounded-xl cursor-pointer transition-colors"
                               :class="dragging ? 'border-amber-400 bg-amber-50' : 'border-gray-300 hover:border-amber-400 hover:bg-gray-50'"
                               @dragover.prevent="dragging = true"
                               @dragleave.prevent="dragging = false"
                               @drop.prevent="dragging = false">
                            <div class="text-center">
                                <div class="mx-auto h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center mb-2">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                </div>
                                <p class="text-sm font-medium text-gray-700">
                                    <span class="text-amber-600">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP up to 2MB each</p>
                            </div>
                            <input type="file" name="images[]" multiple accept="image/*" class="hidden" @change="
                                previews = [];
                                for (const file of $event.target.files) {
                                    const reader = new FileReader();
                                    reader.onload = e => previews = [...previews, e.target.result];
                                    reader.readAsDataURL(file);
                                }
                            ">
                        </label>
                        <div x-show="previews.length > 0" x-transition class="grid grid-cols-4 gap-3 mt-4">
                            <template x-for="(src, i) in previews" :key="i">
                                <div class="relative aspect-square">
                                    <img :src="src" class="h-full w-full rounded-lg object-cover">
                                </div>
                            </template>
                        </div>
                    </div>
                    @error('images') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    @error('images.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- SEO --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">SEO</h2>
                    <div class="space-y-5">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $product->meta_title) }}" placeholder="SEO title for search engines" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('meta_title') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="2" placeholder="Brief description for search engine results" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">{{ old('meta_description', $product->meta_description) }}</textarea>
                            @error('meta_description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column (1/3) --}}
            <div class="space-y-6">
                {{-- Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">Status</h2>
                    <div class="space-y-4">
                        <label class="flex items-center justify-between cursor-pointer" x-data="{ enabled: {{ old('is_active', $product->is_active) ? 'true' : 'false' }} }">
                            <span class="text-sm font-medium text-gray-700">Active</span>
                            <input type="hidden" name="is_active" value="0">
                            <button type="button" @click="enabled = !enabled" :class="enabled ? 'bg-amber-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                <span :class="enabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                            <input type="hidden" name="is_active" :value="enabled ? '1' : '0'">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer" x-data="{ enabled: {{ old('is_featured', $product->is_featured) ? 'true' : 'false' }} }">
                            <span class="text-sm font-medium text-gray-700">Featured</span>
                            <input type="hidden" name="is_featured" value="0">
                            <button type="button" @click="enabled = !enabled" :class="enabled ? 'bg-amber-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                <span :class="enabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                            <input type="hidden" name="is_featured" :value="enabled ? '1' : '0'">
                        </label>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">Pricing</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (₱) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            </div>
                            @error('price') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-1">Sale Price (₱) <span class="text-xs text-gray-400">Optional</span></label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0" class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            </div>
                            <p class="mt-1 text-xs text-gray-400">Must be lower than regular price. Leave blank if no sale.</p>
                            @error('sale_price') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1">Cost Price (₱)</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0" class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            </div>
                            @error('cost_price') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Inventory --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">Inventory</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity <span class="text-red-500">*</span></label>
                            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('stock_quantity') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                            <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('low_stock_threshold') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight (g)</label>
                            <input type="number" name="weight" id="weight" value="{{ old('weight', $product->weight) }}" step="0.01" min="0" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('weight') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Category --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">Category</h2>
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Select Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            <option value="">Choose a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.products.show', $product) }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-4 py-2 rounded-lg transition-colors">Cancel</a>
            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-6 py-2 rounded-lg transition-colors inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Update Product
            </button>
        </div>
    </form>
</div>
@endsection
