
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
                        
                        <div class="mt-4">
                            <button type="button" id="add-shipping-address" class="text-amber-600 hover:text-amber-700 font-medium text-sm">
                                + Add New Shipping Address
                            </button>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-600 mb-4">No shipping addresses found.</p>
                            <button type="button" id="add-shipping-address" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                                Add Shipping Address
                            </button>
                        </div>
                    @endif
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
                        @if($billingAddresses->count() > 0)
                            <div class="space-y-3">
                                @foreach($billingAddresses as $address)
                                    <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="billing_address_id" value="{{ $address->id }}" 
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
                            
                            <div class="mt-4">
                                <button type="button" id="add-billing-address" class="text-amber-600 hover:text-amber-700 font-medium text-sm">
                                    + Add New Billing Address
                                </button>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-600 mb-4">No billing addresses found.</p>
                                <button type="button" id="add-billing-address" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                                    Add Billing Address
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>
                    
                    <div class="space-y-4">
                        <!-- Credit Card -->
                        <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="payment_method" value="credit_card" class="mt-1 text-amber-600 focus:ring-amber-500" checked required>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Credit Card</div>
                                <div class="text-sm text-gray-600">Pay securely with your credit card</div>
                            </div>
                            <div class="flex space-x-2">
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">VISA</span>
                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">MC</span>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">AMEX</span>
                            </div>
                        </label>

                        <!-- PayPal -->
                        <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="payment_method" value="paypal" class="mt-1 text-amber-600 focus:ring-amber-500" required>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">PayPal</div>
                                <div class="text-sm text-gray-600">Pay with your PayPal account</div>
                            </div>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">PayPal</span>
                        </label>

                        <!-- Stripe -->
                        <label class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="payment_method" value="stripe" class="mt-1 text-amber-600 focus:ring-amber-500" required>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">Stripe</div>
                                <div class="text-sm text-gray-600">Secure payment processing</div>
                            </div>
                            <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">Stripe</span>
                        </label>
                    </div>

                    <!-- Credit Card Details -->
                    <div id="credit-card-details" class="mt-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                                <input type="text" name="card_number" placeholder="1234 5678 9012 3456" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                                <input type="text" name="card_name" placeholder="John Doe" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                                <input type="text" name="card_expiry" placeholder="MM/YY" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                                <input type="text" name="card_cvv" placeholder="123" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>
                    
                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" 
                                         class="w-16 h-16 object-cover rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-sm text-gray-600">Qty: {{ $item->quantity }}</div>
                                    </div>
                                </div>
                                <div class="font-medium text-gray-900">${{ number_format(($item->product->is_on_sale ? $item->product->sale_price : $item->product->price) * $item->quantity, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="border-t border-gray-200 mt-6 pt-6 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium">${{ number_format($shippingAmount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-medium">${{ number_format($taxAmount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full mt-6 bg-amber-600 text-white py-3 px-4 rounded-lg hover:bg-amber-700 transition-colors font-medium">
                        Place Order
                    </button>
                </div>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 sticky top-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>
                
                <div class="space-y-4">
                    @foreach($cartItems as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" 
                                     class="w-16 h-16 object-cover rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $item->product->name }}</div>
                                    <div class="text-sm text-gray-600">Qty: {{ $item->quantity }}</div>
                                </div>
                            </div>
                            <div class="font-medium text-gray-900">${{ number_format(($item->product->is_on_sale ? $item->product->sale_price : $item->product->price) * $item->quantity, 2) }}</div>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t border-gray-200 mt-6 pt-6 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium">${{ number_format($shippingAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-medium">${{ number_format($taxAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle billing address section based on checkbox
    const sameAsShippingCheckbox = document.getElementById('same-as-shipping');
    const billingAddressSection = document.getElementById('billing-address-section');
    
    sameAsShippingCheckbox.addEventListener('change', function() {
        if (this.checked) {
            billingAddressSection.style.display = 'none';
        } else {
            billingAddressSection.style.display = 'block';
        }
    });
    
    // Handle payment method selection
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const creditCardDetails = document.getElementById('credit-card-details');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'credit_card') {
                creditCardDetails.style.display = 'block';
            } else {
                creditCardDetails.style.display = 'none';
            }
        });
    });
    
    // Initialize credit card details visibility
    if (document.querySelector('input[name="payment_method"]:checked').value !== 'credit_card') {
        creditCardDetails.style.display = 'none';
    }
    
    // Add new address buttons
    document.getElementById('add-shipping-address').addEventListener('click', function() {
        alert('Add new shipping address functionality would be implemented here.');
    });
    
    document.getElementById('add-billing-address').addEventListener('click', function() {
        alert('Add new billing address functionality would be implemented here.');
    });
    
    // Form submission with PayPal temporary checkout
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const shippingAddressId = document.querySelector('input[name="shipping_address_id"]:checked')?.value;
        const billingAddressId = document.querySelector('input[name="billing_address_id"]:checked')?.value;
        
        // Basic validation
        if (!shippingAddressId) {
            alert('Please select a shipping address.');
            return;
        }
        
        if (!sameAsShippingCheckbox.checked && !billingAddressId) {
            alert('Please select a billing address.');
            return;
        }
        
        // Disable submit button to prevent double submission
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';
        
        // Prepare order data
        const orderData = {
            _token: document.querySelector('input[name="_token"]').value,
            shipping_address_id: shippingAddressId,
            billing_address_id: sameAsShippingCheckbox.checked ? shippingAddressId : billingAddressId,
            payment_method: paymentMethod,
            same_as_shipping: sameAsShippingCheckbox.checked,
            order_notes: document.querySelector('textarea[name="order_notes"]')?.value || ''
        };
        
        // Add credit card details if credit card is selected
        if (paymentMethod === 'credit_card') {
            orderData.card_number = document.querySelector('input[name="card_number"]').value;
            orderData.card_name = document.querySelector('input[name="card_name"]').value;
            orderData.card_expiry = document.querySelector('input[name="card_expiry"]').value;
            orderData.card_cvv = document.querySelector('input[name="card_cvv"]').value;
        }
        
        // Submit order to backend
        fetch('{{ route("checkout.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (paymentMethod === 'paypal') {
                    // Simulate PayPal payment process
                    alert('PayPal payment simulation: Order #' + data.order_number + ' has been created successfully!\n\nIn a real implementation, you would be redirected to PayPal for payment.');
                    
                    // Simulate successful PayPal payment and redirect to success page
                    setTimeout(() => {
                        window.location.href = '/checkout/success/' + data.order_number;
                    }, 2000);
                } else if (paymentMethod === 'stripe') {
                    alert('Stripe payment simulation: Order #' + data.order_number + ' has been created successfully!\n\nIn a real implementation, Stripe payment processing would occur here.');
                    setTimeout(() => {
                        window.location.href = '/checkout/success/' + data.order_number;
                    }, 2000);
                } else {
                    // Credit card processing
                    alert('Credit card payment simulation: Order #' + data.order_number + ' has been created successfully!\n\nIn a real implementation, credit card processing would occur here.');
                    setTimeout(() => {
                        window.location.href = '/checkout/success/' + data.order_number;
                    }, 2000);
                }
            } else {
                alert('Error: ' + (data.message || 'Something went wrong. Please try again.'));
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your order. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });
});
</script>
@endsection
