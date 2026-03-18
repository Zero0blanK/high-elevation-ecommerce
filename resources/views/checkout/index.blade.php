
@extends('layouts.app')

@section('title', 'Checkout - ' . config('ecommerce.store.name'))
@section('description', 'Complete your coffee bean order with secure checkout.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        <a href="{{ route('cart.index') }}" class="text-amber-600 hover:text-amber-700 font-medium">
            ← Back to Cart
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <form id="checkout-form" class="space-y-8">
                @csrf
                
                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Shipping Address</h2>
                    
                    <div id="shipping-addresses-container">
                        @if($shippingAddresses->count() > 0)
                            <div class="space-y-3">
                                @foreach($shippingAddresses as $address)
                                    <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="shipping_address_id" value="{{ $address->id }}" 
                                               class="mt-1 text-amber-600 focus:ring-amber-500" 
                                               {{ $address->is_default ? 'checked' : '' }} required>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ $address->full_name }}</div>
                                            @if($address->company)
                                                <div class="text-sm text-gray-600">{{ $address->company }}</div>
                                            @endif
                                            <div class="text-sm text-gray-600">{{ $address->full_address }}</div>
                                            @if($address->is_default)
                                                <span class="inline-block mt-1 px-2 py-1 text-xs bg-amber-100 text-amber-800 rounded">Default</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8" id="no-shipping-address">
                                <p class="text-gray-600 mb-4">No shipping addresses found.</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" onclick="openAddressModal('shipping')" class="text-amber-600 hover:text-amber-700 font-medium text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add New Shipping Address
                        </button>
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Billing Address</h2>
                    
                    <div class="mb-4">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="same-as-shipping" class="text-amber-600 focus:ring-amber-500">
                            <span class="text-sm text-gray-700">Same as shipping address</span>
                        </label>
                    </div>
                    
                    <div id="billing-address-section">
                        <div id="billing-addresses-container">
                            @if($billingAddresses->count() > 0)
                                <div class="space-y-3">
                                    @foreach($billingAddresses as $address)
                                        <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input type="radio" name="billing_address_id" value="{{ $address->id }}" 
                                                   class="mt-1 text-amber-600 focus:ring-amber-500" 
                                                   {{ $address->is_default ? 'checked' : '' }}>
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900">{{ $address->full_name }}</div>
                                                @if($address->company)
                                                    <div class="text-sm text-gray-600">{{ $address->company }}</div>
                                                @endif
                                                <div class="text-sm text-gray-600">{{ $address->full_address }}</div>
                                                @if($address->is_default)
                                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-amber-100 text-amber-800 rounded">Default</span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8" id="no-billing-address">
                                    <p class="text-gray-600 mb-4">No billing addresses found.</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-4">
                            <button type="button" onclick="openAddressModal('billing')" class="text-amber-600 hover:text-amber-700 font-medium text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add New Billing Address
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>
                    
                    <div class="space-y-4">
                        <!-- Credit Card via Stripe -->
                        <label class="payment-method-option flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" data-method="credit_card">
                            <input type="radio" name="payment_method" value="credit_card" class="mt-1 text-amber-600 focus:ring-amber-500" checked required>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Credit / Debit Card</div>
                                <div class="text-sm text-gray-600">Pay securely with your card via Stripe</div>
                            </div>
                            <div class="flex space-x-2">
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">VISA</span>
                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">MC</span>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">AMEX</span>
                            </div>
                        </label>

                        <!-- Stripe Card Element Container -->
                        <div id="stripe-card-section" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div id="card-element" class="p-3 bg-white border border-gray-300 rounded-lg"></div>
                            <div id="card-errors" class="mt-2 text-sm text-red-600"></div>
                        </div>

                        <!-- PayPal -->
                        <label class="payment-method-option flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" data-method="paypal">
                            <input type="radio" name="payment_method" value="paypal" class="mt-1 text-amber-600 focus:ring-amber-500">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">PayPal</div>
                                <div class="text-sm text-gray-600">Pay with your PayPal account</div>
                            </div>
                            <span class="text-xs bg-blue-500 text-white px-2 py-1 rounded font-semibold">PayPal</span>
                        </label>

                        <!-- PayPal Button Container -->
                        <div id="paypal-button-container" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200"></div>

                        <!-- GCash -->
                        <label class="payment-method-option flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" data-method="gcash">
                            <input type="radio" name="payment_method" value="gcash" class="mt-1 text-amber-600 focus:ring-amber-500">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">GCash</div>
                                <div class="text-sm text-gray-600">Pay with your GCash wallet</div>
                            </div>
                            <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded font-semibold">GCash</span>
                        </label>

                        <!-- GCash Info Section -->
                        <div id="gcash-section" class="hidden mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-blue-800">GCash Payment</p>
                                    <p class="text-sm text-blue-600">You will be redirected to GCash to complete your payment.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cash on Delivery -->
                        <label class="payment-method-option flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" data-method="cod">
                            <input type="radio" name="payment_method" value="cod" class="mt-1 text-amber-600 focus:ring-amber-500">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Cash on Delivery</div>
                                <div class="text-sm text-gray-600">Pay when you receive your order</div>
                            </div>
                            <span class="text-xs bg-gray-600 text-white px-2 py-1 rounded">COD</span>
                        </label>
                    </div>

                    <!-- Order Notes -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order Notes (Optional)</label>
                        <textarea name="order_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" placeholder="Add any special instructions for your order..."></textarea>
                    </div>
                </div>

                <!-- Place Order Button (for non-PayPal methods) -->
                <div id="standard-checkout-btn">
                    <button type="submit" id="submit-btn" class="w-full bg-amber-600 text-white py-4 px-6 rounded-lg hover:bg-amber-700 transition-colors font-semibold text-lg flex items-center justify-center gap-2">
                        <span id="submit-btn-text">Place Order - ${{ number_format($total, 2) }}</span>
                        <svg id="submit-spinner" class="hidden animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 sticky top-24">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>
                
                <div class="space-y-4 max-h-64 overflow-y-auto">
                    @foreach($cartItems as $item)
                        <div class="flex items-center space-x-3">
                            <img src="{{ $item->product->primaryImage?->image_url ?? '/images/placeholder-coffee.jpg' }}" alt="{{ $item->product->name }}" 
                                 class="w-14 h-14 object-cover rounded-lg flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 text-sm truncate">{{ $item->product->name }}</div>
                                <div class="text-xs text-gray-600">Qty: {{ $item->quantity }}</div>
                            </div>
                            <div class="font-medium text-gray-900 text-sm">${{ number_format(($item->product->is_on_sale ? $item->product->sale_price : $item->product->price) * $item->quantity, 2) }}</div>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t border-gray-200 mt-6 pt-6 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium">{{ $shippingAmount > 0 ? '$' . number_format($shippingAmount, 2) : 'FREE' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-medium">${{ number_format($taxAmount, 2) }}</span>
                    </div>
                    <div class="border-t pt-3">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-amber-600">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center gap-2 text-green-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span class="text-sm font-medium">Secure Checkout</span>
                    </div>
                    <p class="text-xs text-green-600 mt-1">Your payment information is encrypted and secure.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div id="address-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAddressModal()"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="address-form">
                @csrf
                <input type="hidden" id="address-type" name="type" value="shipping">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Add New Address</h3>
                        <button type="button" onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company (Optional)</label>
                            <input type="text" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 *</label>
                            <input type="text" name="address_line_1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" placeholder="Street address">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                            <input type="text" name="address_line_2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" placeholder="Apartment, suite, unit, etc.">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                <input type="text" name="city" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State/Province *</label>
                                <input type="text" name="state" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code *</label>
                                <input type="text" name="postal_code" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                                <select name="country" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                                    <option value="PH">Philippines</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="AU">Australia</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="is_default" id="is_default" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                            <label for="is_default" class="ml-2 block text-sm text-gray-700">Set as default address</label>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" id="save-address-btn" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:w-auto sm:text-sm">
                        Save Address
                    </button>
                    <button type="button" onclick="closeAddressModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Stripe JS -->
<script src="https://js.stripe.com/v3/"></script>

<!-- PayPal JS SDK -->
@if(config('services.paypal.client_id'))
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=USD"></script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Stripe
    const stripeKey = '{{ config('services.stripe.key') }}';
    let stripe, cardElement;

    if (stripeKey && stripeKey.length > 0) {
        try {
            stripe = Stripe(stripeKey);
            const elements = stripe.elements();
            cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#374151',
                        fontFamily: 'Inter, system-ui, sans-serif',
                        '::placeholder': { color: '#9CA3AF' }
                    },
                    invalid: {
                        color: '#dc2626',
                    }
                }
            });
            cardElement.mount('#card-element');
            
            cardElement.on('change', function(event) {
                const displayError = document.getElementById('card-errors');
                displayError.textContent = event.error ? event.error.message : '';
            });
            
            // Store for form submission
            window.stripeInstance = stripe;
            window.cardElementInstance = cardElement;
        } catch (e) {
            console.error('Stripe initialization error:', e);
            document.getElementById('stripe-card-section').innerHTML = '<p class="text-red-600 p-4">Credit card payment is currently unavailable. Please use another payment method.</p>';
        }
    } else {
        document.getElementById('stripe-card-section').innerHTML = '<p class="text-amber-600 p-4">Credit card payment is not configured. Please use another payment method or contact support.</p>';
    }

    // Payment method switching
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const method = this.value;
            
            // Hide all payment sections
            document.getElementById('stripe-card-section').classList.add('hidden');
            document.getElementById('paypal-button-container').classList.add('hidden');
            document.getElementById('gcash-section').classList.add('hidden');
            document.getElementById('standard-checkout-btn').classList.remove('hidden');
            
            // Show relevant section
            if (method === 'credit_card') {
                document.getElementById('stripe-card-section').classList.remove('hidden');
            } else if (method === 'paypal') {
                document.getElementById('paypal-button-container').classList.remove('hidden');
                document.getElementById('standard-checkout-btn').classList.add('hidden');
                initPayPalButtons();
            } else if (method === 'gcash') {
                document.getElementById('gcash-section').classList.remove('hidden');
            }
        });
    });

    // Initialize default state - show credit card section
    document.getElementById('stripe-card-section').classList.remove('hidden');
});

// PayPal integration
function initPayPalButtons() {
    const container = document.getElementById('paypal-button-container');
    if (container.hasChildNodes()) return; // Already initialized
    
    if (typeof paypal === 'undefined') {
        container.innerHTML = '<p class="text-amber-600 text-center py-4">PayPal is not configured. Please use another payment method.</p>';
        return;
    }
    
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: { value: '{{ number_format($total, 2, '.', '') }}' }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                processPayPalOrder(data.orderID, details);
            });
        },
        onError: function(err) {
            console.error('PayPal error:', err);
            showNotification('PayPal payment failed. Please try again.', 'error');
        }
    }).render('#paypal-button-container');
}

function processPayPalOrder(paypalOrderId, details) {
    const shippingAddressId = document.querySelector('input[name="shipping_address_id"]:checked')?.value;
    const billingAddressId = document.querySelector('input[name="billing_address_id"]:checked')?.value;
    const sameAsShipping = document.getElementById('same-as-shipping').checked;
    
    if (!shippingAddressId) {
        showNotification('Please select a shipping address.', 'error');
        return;
    }
    
    fetch('{{ route("checkout.process") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            shipping_address_id: shippingAddressId,
            billing_address_id: sameAsShipping ? shippingAddressId : billingAddressId,
            payment_method: 'paypal',
            same_as_shipping: sameAsShipping,
            paypal_order_id: paypalOrderId,
            order_notes: document.querySelector('textarea[name="order_notes"]')?.value || ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            showNotification('Error: ' + (data.message || 'Payment processing failed.'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// Billing address toggle
document.getElementById('same-as-shipping').addEventListener('change', function() {
    document.getElementById('billing-address-section').style.display = this.checked ? 'none' : 'block';
});

// Address Modal functions
function openAddressModal(type) {
    document.getElementById('address-type').value = type;
    document.getElementById('modal-title').textContent = 'Add New ' + (type === 'shipping' ? 'Shipping' : 'Billing') + ' Address';
    document.getElementById('address-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddressModal() {
    document.getElementById('address-modal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('address-form').reset();
}

// Address form submission
document.getElementById('address-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const saveBtn = document.getElementById('save-address-btn');
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';
    
    fetch('{{ route("account.addresses.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.address) {
            const address = data.address;
            const type = document.getElementById('address-type').value;
            const container = document.getElementById(type + '-addresses-container');
            
            // Create new address option
            const newAddress = document.createElement('label');
            newAddress.className = 'flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors';
            newAddress.innerHTML = `
                <input type="radio" name="${type}_address_id" value="${address.id}" class="mt-1 text-amber-600 focus:ring-amber-500" checked>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">${address.full_name}</div>
                    <div class="text-sm text-gray-600">${address.full_address || address.address_line_1 + ', ' + address.city}</div>
                </div>
            `;
            
            // Remove "no addresses" message if present
            const noAddressEl = container.querySelector('#no-' + type + '-address');
            if (noAddressEl) noAddressEl.remove();
            
            // Add to container
            let addressList = container.querySelector('.space-y-3');
            if (!addressList) {
                addressList = document.createElement('div');
                addressList.className = 'space-y-3';
                container.prepend(addressList);
            }
            addressList.appendChild(newAddress);
            
            closeAddressModal();
            showNotification('Address added successfully!', 'success');
        } else {
            showNotification(data.message || 'Failed to add address.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save Address';
    });
});

// Main checkout form submission
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const shippingAddressId = document.querySelector('input[name="shipping_address_id"]:checked')?.value;
    const billingAddressId = document.querySelector('input[name="billing_address_id"]:checked')?.value;
    const sameAsShipping = document.getElementById('same-as-shipping').checked;
    
    // Validation
    if (!shippingAddressId) {
        showNotification('Please select a shipping address.', 'error');
        return;
    }
    
    if (!sameAsShipping && !billingAddressId) {
        showNotification('Please select a billing address or check "Same as shipping address".', 'error');
        return;
    }
    
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('submit-btn-text');
    const spinner = document.getElementById('submit-spinner');
    
    submitBtn.disabled = true;
    btnText.textContent = 'Processing...';
    spinner.classList.remove('hidden');
    
    // For Stripe card payments
    if (paymentMethod === 'credit_card' && window.stripeInstance && window.cardElementInstance) {
        window.stripeInstance.createPaymentMethod({
            type: 'card',
            card: window.cardElementInstance,
        }).then(function(result) {
            if (result.error) {
                document.getElementById('card-errors').textContent = result.error.message;
                resetSubmitButton();
            } else {
                submitOrder({
                    shipping_address_id: shippingAddressId,
                    billing_address_id: sameAsShipping ? shippingAddressId : billingAddressId,
                    payment_method: paymentMethod,
                    same_as_shipping: sameAsShipping,
                    stripe_payment_method_id: result.paymentMethod.id,
                    order_notes: document.querySelector('textarea[name="order_notes"]')?.value || ''
                });
            }
        });
    } else if (paymentMethod === 'credit_card') {
        showNotification('Credit card payment is not available. Please use another payment method.', 'error');
        resetSubmitButton();
    } else {
        // For GCash, COD, etc.
        submitOrder({
            shipping_address_id: shippingAddressId,
            billing_address_id: sameAsShipping ? shippingAddressId : billingAddressId,
            payment_method: paymentMethod,
            same_as_shipping: sameAsShipping,
            order_notes: document.querySelector('textarea[name="order_notes"]')?.value || ''
        });
    }
});

function submitOrder(orderData) {
    fetch('{{ route("checkout.process") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // For GCash, redirect to payment URL if provided
            if (data.payment_url) {
                window.location.href = data.payment_url;
            } else {
                window.location.href = data.redirect_url;
            }
        } else {
            showNotification(data.message || 'Something went wrong. Please try again.', 'error');
            resetSubmitButton();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
        resetSubmitButton();
    });
}

function resetSubmitButton() {
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('submit-btn-text');
    const spinner = document.getElementById('submit-spinner');
    
    submitBtn.disabled = false;
    btnText.textContent = 'Place Order - ${{ number_format($total, 2) }}';
    spinner.classList.add('hidden');
}

function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-[100] px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full ${type === 'error' ? 'bg-red-600' : 'bg-green-600'} text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.classList.remove('translate-x-full'), 10);
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAddressModal();
});
</script>
@endpush
