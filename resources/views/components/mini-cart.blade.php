@props(['cartItems' => collect(), 'total' => 0])

<div class="relative" x-data="{ open: false }">
    <!-- Cart Icon -->
    <button @click="open = !open" 
            class="flex items-center text-gray-700 hover:text-amber-600 transition-colors relative">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 3H5a2 2 0 00-2 2v1m2 0h16M7 13v8a2 2 0 002 2h8a2 2 0 002-2v-8m-9 4h4"/>
        </svg>
        
        @if($cartItems->sum('quantity') > 0)
            <span class="absolute -top-2 -right-2 bg-amber-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                {{ $cartItems->sum('quantity') > 99 ? '99+' : $cartItems->sum('quantity') }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
        
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Shopping Cart</h3>
            
            @if($cartItems->count() > 0)
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($cartItems->take(3) as $item)
                        <div class="flex items-center space-x-3">
                            <img src="{{ $item->product->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="w-12 h-12 object-cover rounded">
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $item->product->name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Qty: {{ $item->quantity }} × ${{ number_format($item->product->discounted_price, 2) }}
                                </p>
                            </div>
                            
                            <p class="text-sm font-semibold text-gray-900">
                                ${{ number_format($item->subtotal, 2) }}
                            </p>
                        </div>
                    @endforeach
                    
                    @if($cartItems->count() > 3)
                        <p class="text-xs text-gray-500 text-center pt-2">
                            And {{ $cartItems->count() - 3 }} more items...
                        </p>
                    @endif
                </div>
                
                <div class="border-t pt-3 mt-3">
                    <div class="flex justify-between items-center mb-3">
                        <span class="font-semibold text-gray-900">Total:</span>
                        <span class="font-semibold text-gray-900">${{ number_format($total, 2) }}</span>
                    </div>
                    
                    <div class="space-y-2">
                        <a href="{{ route('cart.index') }}" 
                           class="block w-full text-center bg-gray-100 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                            View Cart
                        </a>
                        <button class="w-full bg-amber-600 text-white py-2 px-4 rounded-md text-sm font-semibold hover:bg-amber-700 transition-colors">
                            Checkout
                        </button>
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 3H5a2 2 0 00-2 2v1m2 0h16M7 13v8a2 2 0 002 2h8a2 2 0 002-2v-8m-9 4h4"/>
                    </svg>
                    <p class="text-sm text-gray-500 mb-3">Your cart is empty</p>
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center text-amber-600 hover:text-amber-700 text-sm font-medium">
                        Start Shopping
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>