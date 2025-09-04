@extends('layouts.app')

@section('title', $product->meta_title ?: $product->name . ' - Premium Coffee Beans')
@section('description', $product->meta_description ?: $product->short_description)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
        <!-- Breadcrumb -->
        <nav class="mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4 text-sm">
                <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700">Products</a></li>
                @if($product->category)
                    <li><span class="text-gray-400">/</span></li>
                    <li><a href="{{ route('products.category', $product->category->slug) }}" class="text-gray-500 hover:text-gray-700">{{ $product->category->name }}</a></li>
                @endif
                <li><span class="text-gray-400">/</span></li>
                <li><span class="text-gray-900 font-medium">{{ $product->name }}</span></li>
            </ol>
        </nav>

        <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
            <!-- Image Gallery -->
            <div class="flex flex-col-reverse" x-data="productGallery()">
                <!-- Image selector -->
                @if($product->images && $product->images->count() > 1)
                <div class="hidden mt-6 w-full max-w-2xl mx-auto sm:block lg:max-w-none">
                    <div class="grid grid-cols-4 gap-6">
                        @foreach($product->images as $index => $image)
                            <button @click="currentImage = {{ $index }}" 
                                    :class="{ 'ring-2 ring-amber-500': currentImage === {{ $index }} }"
                                    class="relative h-24 bg-white rounded-md flex items-center justify-center text-sm font-medium uppercase text-gray-900 cursor-pointer hover:bg-gray-50 focus:outline-none focus:ring focus:ring-offset-4 focus:ring-amber-500">
                                <span class="sr-only">Image {{ $index + 1 }}</span>
                                <span class="absolute inset-0 rounded-md overflow-hidden">
                                    <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}" class="w-full h-full object-center object-cover">
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Main image -->
                <div class="w-full aspect-w-1 aspect-h-1">
                    @if($product->images && $product->images->count() > 0)
                        @foreach($product->images as $index => $image)
                            <div x-show="currentImage === {{ $index }}" class="w-full h-96 bg-gray-300 rounded-lg overflow-hidden">
                                <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}" 
                                     class="w-full h-full object-center object-cover sm:rounded-lg">
                            </div>
                        @endforeach
                    @else
                        <div class="w-full h-96 bg-gray-300 rounded-lg overflow-hidden">
                            <img src="{{ $product->image ?? '/images/placeholder-coffee.jpg' }}" alt="{{ $product->name }}" 
                                 class="w-full h-full object-center object-cover sm:rounded-lg">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product info -->
            <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ $product->name }}</h1>

                <div class="mt-3">
                    <h2 class="sr-only">Product information</h2>
                    <div class="flex items-center space-x-4">
                        @if($product->is_on_sale && $product->sale_price)
                            <p class="text-3xl text-red-600 font-bold">${{ number_format($product->sale_price, 2) }}</p>
                            <p class="text-xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</p>
                            <span class="bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                Save ${{ number_format($product->price - $product->sale_price, 2) }}
                            </span>
                        @else
                            <p class="text-3xl text-gray-900 font-bold">${{ number_format($product->price, 2) }}</p>
                        @endif
                    </div>
                </div>

                <!-- Product details -->
                <div class="mt-6">
                    <h3 class="sr-only">Description</h3>
                    <div class="text-base text-gray-900 space-y-6">
                        <p>{{ $product->short_description }}</p>
                    </div>
                </div>

                <!-- Product specifications -->
                <div class="mt-8">
                    <h3 class="text-sm font-medium text-gray-900">Specifications</h3>
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        @if($product->roast_level)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Roast Level</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $product->roast_level) }}</dd>
                        </div>
                        @endif
                        @if($product->grind_type)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Grind Type</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $product->grind_type) }}</dd>
                        </div>
                        @endif
                        @if($product->weight)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Weight</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->weight }} lb</dd>
                        </div>
                        @endif
                        @if($product->origin)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">Origin</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->origin }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Stock status -->
                <div class="mt-6">
                    @if($product->stock_quantity > 0)
                        <div class="flex items-center space-x-2 text-green-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">In Stock</span>
                            @if($product->stock_quantity <= 10)
                                <span class="text-amber-600 text-sm">(Only {{ $product->stock_quantity }} left)</span>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center space-x-2 text-red-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">Out of Stock</span>
                        </div>
                    @endif
                </div>
                <div class="mt-8 space-y-4" x-data="addToCartForm()">
                    <!-- Add to cart button --> 
                    <form action="{{ route('cart.add') }}" method="POST" class="mt-8" @submit="handleSubmit">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <!-- Quantity selector -->
                        <div class="mb-6">
                            <label for="quantity" class="block text-sm font-medium text-gray-900 mb-2">Quantity</label>
                            <div class="flex items-center space-x-3">
                                <button type="button" @click="decreaseQuantity()" 
                                        :disabled="quantity <= 1"
                                        :class="quantity <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                                        class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <input type="number" name="quantity" id="quantity" x-model="quantity" min="1" max="{{ $product->stock_quantity }}"
                                    @input="validateQuantity()"
                                    class="w-20 text-center border-gray-300 rounded-md focus:ring-amber-500 focus:border-amber-500">
                                <button type="button" @click="increaseQuantity()" 
                                        :disabled="quantity >= maxQuantity"
                                        :class="quantity >= maxQuantity ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                                        class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="submit" 
                                :disabled="!inStock || quantity <= 0"
                                :class="(!inStock || quantity <= 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-amber-600 hover:bg-amber-700'"
                                class="w-full py-3 px-6 rounded-md text-white font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                            Add to Cart
                        </button>
                    </form>

                    <!-- Buy Now button -->
                    <form action="{{ route('checkout.buyNow') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" :value="quantity">
                        <button type="submit" 
                                :disabled="!inStock || quantity <= 0"
                                :class="(!inStock || quantity <= 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                                class="w-full py-3 px-6 rounded-md text-white font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Buy Now
                        </button>
                    </form>
                </div>
                <!-- Additional info -->
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <h3 class="text-sm font-medium text-gray-900">Shipping & Returns</h3>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>• Free shipping on orders over ${{ config('ecommerce.shipping.free_shipping_threshold') }}</p>
                        <p>• Roasted fresh and shipped within 24 hours</p>
                        <p>• 30-day satisfaction guarantee</p>
                        <p>• Secure packaging to preserve freshness</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product description -->
        @if($product->description)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Product Description</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        @endif

        <!-- Related products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">You might also like</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            <div class="aspect-w-1 aspect-h-1">
                                <img src="{{ $related->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}" 
                                     alt="{{ $related->name }}" 
                                     class="w-full h-48 object-cover">
                            </div>
                            <div class="p-4">
                                <h3 class="font-medium text-gray-900 mb-2 line-clamp-1">{{ $related->name }}</h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $related->short_description }}</p>
                                <div class="flex items-center justify-between">
                                    @if($related->isOnSale())
                                        <div class="flex items-center space-x-2">
                                            <span class="font-bold text-red-600">${{ number_format($related->sale_price, 2) }}</span>
                                            <span class="text-sm text-gray-500 line-through">${{ number_format($related->price, 2) }}</span>
                                        </div>
                                    @else
                                        <span class="font-bold text-gray-900">${{ number_format($related->price, 2) }}</span>
                                    @endif
                                    <a href="{{ route('products.show', $related->slug) }}" 
                                       class="text-amber-600 hover:text-amber-700 text-sm font-medium">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        function productGallery() {
            return {
                currentImage: 0
            }
        }

        function addToCartForm() {
            return {
                quantity: 1,
                maxQuantity: {{ $product->stock_quantity}},
                inStock: {{ $product->stock_quantity > 0 ? 'true' : 'false' }},
                
                decreaseQuantity() {
                    if (this.quantity > 1) {
                        this.quantity--;
                    }
                },
                
                increaseQuantity() {
                    if (this.quantity < this.maxQuantity) {
                        this.quantity++;
                    }
                },
                
                validateQuantity() {
                    if (this.quantity < 1) {
                        this.quantity = 1;
                    } else if (this.quantity > this.maxQuantity) {
                        this.quantity = this.maxQuantity;
                    }
                },
                
                handleSubmit(event) {
                    if (!this.inStock || this.quantity <= 0) {
                        event.preventDefault();
                    }
                }
            }
        }
    </script>
@endsection