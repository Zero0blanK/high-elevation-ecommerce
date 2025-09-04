@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Settings</h1>

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

    <!-- Settings Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button onclick="showTab('general')" class="tab-button active border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    General
                </button>
                <button onclick="showTab('email')" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Email
                </button>
                <button onclick="showTab('payment')" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Payment
                </button>
                <button onclick="showTab('shipping')" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Shipping
                </button>
            </nav>
        </div>

        <!-- General Settings Tab -->
        <div id="general-tab" class="tab-content p-6">
            <form action="{{ route('admin.settings.general') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                        <input type="text" name="site_name" id="site_name" 
                               value="{{ old('site_name', $settings['general']['site_name'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="site_email" class="block text-sm font-medium text-gray-700 mb-2">Site Email</label>
                        <input type="email" name="site_email" id="site_email" 
                               value="{{ old('site_email', $settings['general']['site_email'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-2">Site Phone</label>
                        <input type="text" name="site_phone" id="site_phone" 
                               value="{{ old('site_phone', $settings['general']['site_phone'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" id="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="USD" {{ ($settings['general']['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ ($settings['general']['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ ($settings['general']['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                        <textarea name="site_description" id="site_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('site_description', $settings['general']['site_description'] ?? '') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="site_address" class="block text-sm font-medium text-gray-700 mb-2">Site Address</label>
                        <textarea name="site_address" id="site_address" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('site_address', $settings['general']['site_address'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                        <input type="file" name="logo" id="logo" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @if(isset($settings['general']['logo']))
                            <img src="{{ asset('storage/' . $settings['general']['logo']) }}" alt="Current Logo" class="mt-2 h-16">
                        @endif
                    </div>

                    <div>
                        <label for="favicon" class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                        <input type="file" name="favicon" id="favicon" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @if(isset($settings['general']['favicon']))
                            <img src="{{ asset('storage/' . $settings['general']['favicon']) }}" alt="Current Favicon" class="mt-2 h-8">
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Save General Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Email Settings Tab -->
        <div id="email-tab" class="tab-content p-6 hidden">
            <form action="{{ route('admin.settings.email') }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mail_driver" class="block text-sm font-medium text-gray-700 mb-2">Mail Driver</label>
                        <select name="mail_driver" id="mail_driver" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="smtp" {{ ($settings['email']['mail_driver'] ?? '') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ ($settings['email']['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="mailgun" {{ ($settings['email']['mail_driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="ses" {{ ($settings['email']['mail_driver'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                        </select>
                    </div>

                    <div>
                        <label for="mail_host" class="block text-sm font-medium text-gray-700 mb-2">Mail Host</label>
                        <input type="text" name="mail_host" id="mail_host" 
                               value="{{ old('mail_host', $settings['email']['mail_host'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="mail_port" class="block text-sm font-medium text-gray-700 mb-2">Mail Port</label>
                        <input type="number" name="mail_port" id="mail_port" 
                               value="{{ old('mail_port', $settings['email']['mail_port'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="mail_encryption" class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                        <select name="mail_encryption" id="mail_encryption" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">None</option>
                            <option value="tls" {{ ($settings['email']['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['email']['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>

                    <div>
                        <label for="mail_username" class="block text-sm font-medium text-gray-700 mb-2">Mail Username</label>
                        <input type="text" name="mail_username" id="mail_username" 
                               value="{{ old('mail_username', $settings['email']['mail_username'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="mail_password" class="block text-sm font-medium text-gray-700 mb-2">Mail Password</label>
                        <input type="password" name="mail_password" id="mail_password" 
                               value="{{ old('mail_password', $settings['email']['mail_password'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="mail_from_address" class="block text-sm font-medium text-gray-700 mb-2">From Address</label>
                        <input type="email" name="mail_from_address" id="mail_from_address" 
                               value="{{ old('mail_from_address', $settings['email']['mail_from_address'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="mail_from_name" class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                        <input type="text" name="mail_from_name" id="mail_from_name" 
                               value="{{ old('mail_from_name', $settings['email']['mail_from_name'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Save Email Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Payment Settings Tab -->
        <div id="payment-tab" class="tab-content p-6 hidden">
            <form action="{{ route('admin.settings.payment') }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="stripe_secret_key" class="block text-sm font-medium text-gray-700 mb-2">Stripe Secret Key</label>
                        <input type="password" name="stripe_secret_key" id="stripe_secret_key" 
                               value="{{ old('stripe_secret_key', $settings['payment']['stripe_secret_key'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="stripe_publishable_key" class="block text-sm font-medium text-gray-700 mb-2">Stripe Publishable Key</label>
                        <input type="password" name="stripe_publishable_key" id="stripe_publishable_key" 
                               value="{{ old('stripe_publishable_key', $settings['payment']['stripe_publishable_key'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="paypal_client_id" class="block text-sm font-medium text-gray-700 mb-2">PayPal Client ID</label>
                        <input type="text" name="paypal_client_id" id="paypal_client_id" 
                               value="{{ old('paypal_client_id', $settings['payment']['paypal_client_id'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="paypal_secret" class="block text-sm font-medium text-gray-700 mb-2">PayPal Secret</label>
                        <input type="password" name="paypal_secret" id="paypal_secret" 
                               value="{{ old('paypal_secret', $settings['payment']['paypal_secret'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="payment_methods" class="block text-sm font-medium text-gray-700 mb-2">Payment Methods</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="stripe_enabled" name="payment_methods[]" value="stripe" 
                                       {{ in_array('stripe', old('payment_methods', $settings['payment']['payment_methods'] ?? [])) ? 'checked' : '' }}
                                       class="mr-2">
                                <label for="stripe_enabled">Stripe</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="paypal_enabled" name="payment_methods[]" value="paypal" 
                                       {{ in_array('paypal', old('payment_methods', $settings['payment']['payment_methods'] ?? [])) ? 'checked' : '' }}
                                       class="mr-2">
                                <label for="paypal_enabled">PayPal</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Save Payment Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Shipping Settings Tab -->
        <div id="shipping-tab" class="tab-content p-6 hidden">
            <form action="{{ route('admin.settings.shipping') }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="shipping_cost" class="block text-sm font-medium text-gray-700 mb-2">Base Shipping Cost</label>
                        <input type="number" step="0.01" name="shipping_cost" id="shipping_cost" 
                               value="{{ old('shipping_cost', $settings['shipping']['shipping_cost'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="free_shipping_threshold" class="block text-sm font-medium text-gray-700 mb-2">Free Shipping Threshold</label>
                        <input type="number" step="0.01" name="free_shipping_threshold" id="free_shipping_threshold" 
                               value="{{ old('free_shipping_threshold', $settings['shipping']['free_shipping_threshold'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="shipping_regions" class="block text-sm font-medium text-gray-700 mb-2">Shipping Regions</label>
                        <textarea name="shipping_regions" id="shipping_regions" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('shipping_regions', $settings['shipping']['shipping_regions'] ?? '') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Enter regions separated by commas (e.g., US,UK,CA)</p>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Save Shipping Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        
        // Show the selected tab content
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        
        // Add active class to clicked tab button
        event.target.classList.add('active');
    }
</script>
@endsection