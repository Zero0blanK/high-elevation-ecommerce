@extends('admin.layouts.app')

@section('title', 'Add New Product')

@section('content')
<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center h-9 w-9 rounded-lg bg-white border border-gray-300 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">Basic Information</h2>
                    <div class="space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Enter product name" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU <span class="text-red-500">*</span></label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required placeholder="e.g. HE-COF-001" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('sku') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="5" placeholder="Describe your product..." class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">{{ old('description') }}</textarea>
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
                                <option value="light" @selected(old('roast_level') === 'light')>Light</option>
                                <option value="medium" @selected(old('roast_level') === 'medium')>Medium</option>
                                <option value="medium-dark" @selected(old('roast_level') === 'medium-dark')>Medium Dark</option>
                                <option value="dark" @selected(old('roast_level') === 'dark')>Dark</option>
                            </select>
                            @error('roast_level') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="grind_type" class="block text-sm font-medium text-gray-700 mb-1">Grind Type</label>
                            <select name="grind_type" id="grind_type" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                <option value="">Select grind type</option>
                                <option value="whole-bean" @selected(old('grind_type') === 'whole-bean')>Whole Bean</option>
                                <option value="coarse" @selected(old('grind_type') === 'coarse')>Coarse</option>
                                <option value="medium" @selected(old('grind_type') === 'medium')>Medium</option>
                                <option value="fine" @selected(old('grind_type') === 'fine')>Fine</option>
                                <option value="extra-fine" @selected(old('grind_type') === 'extra-fine')>Extra Fine</option>
                            </select>
                            @error('grind_type') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="origin" class="block text-sm font-medium text-gray-700 mb-1">Origin</label>
                            <input type="text" name="origin" id="origin" value="{{ old('origin') }}" placeholder="e.g. Ethiopia, Colombia" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('origin') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="flavor_notes" class="block text-sm font-medium text-gray-700 mb-1">Flavor Notes</label>
                            <textarea name="flavor_notes" id="flavor_notes" rows="2" placeholder="e.g. Chocolate, Citrus, Nutty" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">{{ old('flavor_notes') }}</textarea>
                            @error('flavor_notes') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- SEO --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">SEO</h2>
                    <div class="space-y-5">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" placeholder="SEO title for search engines" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('meta_title') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="2" placeholder="Brief description for search engine results" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">{{ old('meta_description') }}</textarea>
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
                        <label class="flex items-center justify-between cursor-pointer" x-data="{ enabled: {{ old('is_active', true) ? 'true' : 'false' }} }">
                            <span class="text-sm font-medium text-gray-700">Active</span>
                            <input type="hidden" name="is_active" value="0">
                            <button type="button" @click="enabled = !enabled" :class="enabled ? 'bg-amber-600' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                <span :class="enabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                            <input type="hidden" name="is_active" :value="enabled ? '1' : '0'">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer" x-data="{ enabled: {{ old('is_featured', false) ? 'true' : 'false' }} }">
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
                                <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0" required placeholder="0.00" class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            </div>
                            @error('price') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-1">Sale Price (₱) <span class="text-xs text-gray-400">Optional</span></label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price') }}" step="0.01" min="0" placeholder="0.00" class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
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
                                <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price') }}" step="0.01" min="0" placeholder="0.00" class="block w-full pl-8 border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
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
                            <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                            <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" min="0" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            <p class="mt-1 text-xs text-gray-500">Stock starts at 0. Use Inventory &gt; Stock In to add stock.</p>
                            @error('low_stock_threshold') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight (g)</label>
                            <input type="number" name="weight" id="weight" value="{{ old('weight') }}" step="0.01" min="0" placeholder="0.00" class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
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
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Images --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="imageUploader()">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5">Images</h2>
                    <label class="relative flex flex-col items-center justify-center px-6 py-8 border-2 border-dashed rounded-xl cursor-pointer transition-colors"
                           :class="dragging ? 'border-amber-400 bg-amber-50' : 'border-gray-300 hover:border-amber-400 hover:bg-gray-50'"
                           @dragover.prevent="dragging = true"
                           @dragleave.prevent="dragging = false"
                           @drop.prevent="dragging = false; handleDrop($event)">
                        <div class="text-center">
                            <div class="mx-auto h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-gray-700">
                                <span class="text-amber-600">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP up to 2MB each</p>
                            <p class="text-xs text-gray-400 mt-0.5">First image will be the primary image</p>
                        </div>
                        <input type="file" id="image-input" multiple accept="image/*" class="hidden" @change="handleFiles($event.target.files)">
                    </label>
                    
                    <!-- Hidden file input for form submission -->
                    <input type="file" name="images[]" id="images-form-input" multiple accept="image/*" class="hidden">
                    
                    <div x-show="previews.length > 0" x-transition class="grid grid-cols-3 gap-3 mt-4">
                        <template x-for="(preview, index) in previews" :key="index">
                            <div class="relative aspect-square group">
                                <img :src="preview.url" class="h-full w-full rounded-lg object-cover">
                                <span x-show="index === 0" class="absolute top-1.5 left-1.5 bg-amber-600 text-white text-xs font-medium px-2 py-0.5 rounded-md">Primary</span>
                                <button type="button" @click="removeImage(index)" class="absolute top-1.5 right-1.5 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    @error('images') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    @error('images.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.products.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-4 py-2 rounded-lg transition-colors">Cancel</a>
            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-6 py-2 rounded-lg transition-colors inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Create Product
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function imageUploader() {
    return {
        previews: [],
        files: [],
        dragging: false,
        
        handleFiles(fileList) {
            for (const file of fileList) {
                if (file.type.startsWith('image/')) {
                    this.files.push(file);
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previews.push({ url: e.target.result, file: file });
                    };
                    reader.readAsDataURL(file);
                }
            }
            this.updateFormInput();
        },
        
        handleDrop(event) {
            this.handleFiles(event.dataTransfer.files);
        },
        
        removeImage(index) {
            this.previews.splice(index, 1);
            this.files.splice(index, 1);
            this.updateFormInput();
        },
        
        updateFormInput() {
            const dataTransfer = new DataTransfer();
            this.files.forEach(file => dataTransfer.items.add(file));
            document.getElementById('images-form-input').files = dataTransfer.files;
        }
    }
}
</script>
@endpush
@endsection
