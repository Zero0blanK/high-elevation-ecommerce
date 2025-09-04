@extends('layouts.app')

@section('title', 'Shopping Cart - ' . config('ecommerce.store.name'))
@section('description', 'Review your selected coffee beans and proceed to checkout.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
        <a href="{{ route('products.index') }}" class="text-amber-600 hover:text-amber-700 font-medium">
            Continue Shopping
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if($cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <div class="flex items-start space-x-4">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                <img src="{{ $item->product->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-24 h-24 object-cover rounded-lg">
                            </div>
                            
                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                            <a href="{{ route('products.show', $item->product->slug) }}" 
                                               class="hover:text-amber-600 transition-colors">
                                                {{ $item->product->name }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">{{ $item->product->category?->name }}</p>
                                        
                                        @if($item->product->roast_level)
                                            <p class="text-sm text-gray-500">
                                                Roast: {{ ucfirst(str_replace('_', ' ', $item->product->roast_level)) }}
                                            </p>
                                        @endif
                                        
                                        @if($item->product->is_on_sale)
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-lg font-bold text-red-600">
                                                    ${{ number_format($item->product->sale_price, 2) }}
                                                </span>
                                                <span class="text-sm text-gray-500 line-through">
                                                    ${{ number_format($item->product->price, 2) }}
                                                </span>
                                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">
                                                    Sale
                                                </span>
                                            </div>
                                        @else
                                            <p class="text-lg font-bold text-gray-900 mt-1">
                                                ${{ number_format($item->product->price, 2) }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    <!-- Remove Button -->
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="ml-4">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-gray-400 hover:text-red-600 transition-colors"
                                                onclick="return confirm('Remove this item from cart?')">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Quantity and Subtotal -->
                                <div class="flex items-center space-x-3">
                                    <label class="text-sm font-medium text-gray-700">Quantity:</label>
                                    <div class="flex items-center border border-gray-300 rounded-md">
                                        <!-- Decrease Quantity -->
                                        <button type="button" 
                                                class="quantity-btn px-3 py-1 text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors {{ $item->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                data-action="decrease"
                                                data-item-id="{{ $item->id }}"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        
                                        <!-- Current Quantity -->
                                        <span class="quantity-display px-3 py-1 text-sm font-medium text-gray-900 min-w-[3rem] text-center" data-item-id="{{ $item->id }}">
                                            {{ $item->quantity }}
                                        </span>
                                        
                                        <!-- Increase Quantity -->
                                        <button type="button" 
                                                class="quantity-btn px-3 py-1 text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors {{ $item->quantity >= $item->product->stock_quantity ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                data-action="increase"
                                                data-item-id="{{ $item->id }}"
                                                {{ $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}>
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    @if($item->product->stock_quantity <= 5)
                                        <span class="text-xs text-orange-600">
                                            Only {{ $item->product->stock_quantity }} left
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900 item-subtotal" data-item-id="{{ $item->id }}">
                                        ${{ number_format($item->total_price ?? (($item->product->is_on_sale ? $item->product->sale_price : $item->product->price) * $item->quantity), 2) }}
                                    </p>
                                    @if($item->quantity > 1)
                                        <p class="text-xs text-gray-500">
                                            ${{ number_format($item->product->is_on_sale ? $item->product->sale_price : $item->product->price, 2) }} each
                                        </p>
                                    @endif
                                </div> 
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Clear Cart Button -->
                <div class="pt-4">
                    <form action="{{ route('cart.clear') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="text-red-600 hover:text-red-700 font-medium text-sm"
                                onclick="return confirm('Are you sure you want to clear your entire cart?')">
                            Clear Entire Cart
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 sticky top-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Items (<span class="cart-items-count">{{ $cartItems->sum('quantity') }}</span>):</span>
                            <span class="text-gray-900 cart-total">${{ number_format($total, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping:</span>
                            <span class="text-gray-900">Free</span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax:</span>
                            <span class="text-gray-900">Calculated at checkout</span>
                        </div>
                        
                        <div class="border-t pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-gray-900">Total:</span>
                                <span class="text-lg font-semibold text-gray-900">${{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="{{ route('checkout.index') }}" class="block w-full bg-amber-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-amber-700 transition-colors focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 text-center">
                            Proceed to Checkout
                        </a>
                        
                        <a href="{{ route('products.index') }}" 
                           class="block w-full text-center bg-gray-100 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                            Continue Shopping
                        </a>
                    </div>
                    
                    <!-- Trust Badges -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-center space-x-4 text-xs text-gray-500">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Secure Checkout
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Free Shipping
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-24 w-24 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 3H5a2 2 0 00-2 2v1m2 0h16M7 13v8a2 2 0 002 2h8a2 2 0 002-2v-8m-9 4h4"/>
                </svg>
                
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your cart is empty</h2>
                <p class="text-gray-600 mb-8">
                    Looks like you haven't added any coffee beans to your cart yet. 
                    Discover our premium selection and find your perfect brew.
                </p>
                
                <div class="space-y-4">
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-amber-600 text-white font-semibold rounded-lg hover:bg-amber-700 transition-colors">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Start Shopping
                    </a>
                    
                    <div class="text-sm text-gray-500">
                        <p>Popular categories:</p>
                        <div class="flex flex-wrap justify-center gap-2 mt-2">
                            @if(isset($categories) && $categories->count() > 0)
                                @foreach($categories->take(3) as $category)
                                    <a href="{{ route('products.category', $category->slug) }}" 
                                       class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs hover:bg-gray-200 transition-colors">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            @else
                                <a href="{{ route('products.index') }}" 
                                   class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs hover:bg-gray-200 transition-colors">
                                    All Products
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Check if CSRF token exists
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    console.log('CSRF Token:', csrfToken ? csrfToken.getAttribute('content') : 'NOT FOUND');
    
    const quantityButtons = document.querySelectorAll('.quantity-btn');
    console.log('Found quantity buttons:', quantityButtons.length);
    
    quantityButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Button clicked:', this.dataset.action, 'Item ID:', this.dataset.itemId);
            
            const action = this.dataset.action;
            const itemId = this.dataset.itemId;
            const url = `/cart/${action}-ajax/${itemId}`;
            
            console.log('Making request to:', url);
            
            // Disable button during request
            this.disabled = true;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    // Update quantity display
                    const quantityDisplay = document.querySelector(`.quantity-display[data-item-id="${itemId}"]`);
                    const currentQuantity = parseInt(quantityDisplay.textContent);
                    const newQuantity = action === 'increase' ? currentQuantity + 1 : currentQuantity - 1;
                    
                    console.log('Updating quantity from', currentQuantity, 'to', newQuantity);
                    
                    if (newQuantity <= 0) {
                        // Remove the item from display
                        this.closest('.bg-white').remove();
                        console.log('Item removed from cart');
                    } else {
                        quantityDisplay.textContent = newQuantity;
                        
                        // Update item subtotal
                        const itemSubtotal = document.querySelector(`.item-subtotal[data-item-id="${itemId}"]`);
                        const updatedItem = data.cartTotals.items.find(item => item.id == itemId);
                        if (updatedItem && itemSubtotal) {
                            itemSubtotal.textContent = `$${updatedItem.total_price.toFixed(2)}`;
                        }
                        
                        // Update button states
                        const decreaseBtn = document.querySelector(`[data-action="decrease"][data-item-id="${itemId}"]`);
                        const increaseBtn = document.querySelector(`[data-action="increase"][data-item-id="${itemId}"]`);
                        
                        if (decreaseBtn) {
                            decreaseBtn.disabled = newQuantity <= 1;
                            decreaseBtn.classList.toggle('opacity-50', newQuantity <= 1);
                            decreaseBtn.classList.toggle('cursor-not-allowed', newQuantity <= 1);
                        }
                    }
                    
                    // Update cart totals
                    const cartTotal = document.querySelector('.cart-total');
                    const cartItemsCount = document.querySelector('.cart-items-count');
                    
                    if (cartTotal) {
                        cartTotal.textContent = `$${data.cartTotals.subtotal.toFixed(2)}`;
                    }
                    if (cartItemsCount) {
                        cartItemsCount.textContent = data.cartTotals.total_items;
                    }
                    
                } else {
                    console.error('Error:', data.message);
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
            });
        });
    });
});
</script>
@endsection