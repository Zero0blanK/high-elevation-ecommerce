@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto" x-data="{ activeTab: 'general' }">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your application configuration.</p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <svg class="h-5 w-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <svg class="h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabs Navigation --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200 px-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                @foreach(['general' => 'General', 'email' => 'Email', 'payment' => 'Payment', 'shipping' => 'Shipping'] as $tab => $label)
                    <button @click="activeTab = '{{ $tab }}'"
                            :class="activeTab === '{{ $tab }}' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- General Settings Tab --}}
        <div x-show="activeTab === 'general'" x-cloak>
            <form action="{{ route('admin.settings.general') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                            <input type="text" name="site_name" id="site_name"
                                   value="{{ old('site_name', $settings['general']['site_name'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('site_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="site_email" class="block text-sm font-medium text-gray-700 mb-1">Site Email</label>
                            <input type="email" name="site_email" id="site_email"
                                   value="{{ old('site_email', $settings['general']['site_email'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('site_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-1">Site Phone</label>
                            <input type="text" name="site_phone" id="site_phone"
                                   value="{{ old('site_phone', $settings['general']['site_phone'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('site_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="site_description" class="block text-sm font-medium text-gray-700 mb-1">Site Description</label>
                            <input type="text" name="site_description" id="site_description"
                                   value="{{ old('site_description', $settings['general']['site_description'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('site_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="site_address" class="block text-sm font-medium text-gray-700 mb-1">Site Address</label>
                        <textarea name="site_address" id="site_address" rows="3"
                                  class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">{{ old('site_address', $settings['general']['site_address'] ?? '') }}</textarea>
                        @error('site_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                            <select name="timezone" id="timezone"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                @foreach(['UTC', 'America/New_York', 'America/Chicago', 'America/Denver', 'America/Los_Angeles', 'Europe/London', 'Europe/Paris', 'Asia/Tokyo', 'Asia/Manila', 'Australia/Sydney'] as $tz)
                                    <option value="{{ $tz }}" {{ ($settings['general']['timezone'] ?? 'UTC') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                @endforeach
                            </select>
                            @error('timezone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                            <select name="currency" id="currency"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                @foreach(['USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro', 'GBP' => 'GBP - British Pound', 'PHP' => 'PHP - Philippine Peso', 'JPY' => 'JPY - Japanese Yen', 'AUD' => 'AUD - Australian Dollar', 'CAD' => 'CAD - Canadian Dollar'] as $code => $label)
                                    <option value="{{ $code }}" {{ ($settings['general']['currency'] ?? 'USD') == $code ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('currency') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">Save General Settings</button>
                </div>
            </form>
        </div>

        {{-- Email Settings Tab --}}
        <div x-show="activeTab === 'email'" x-cloak>
            <form action="{{ route('admin.settings.email') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="mail_driver" class="block text-sm font-medium text-gray-700 mb-1">Mail Driver</label>
                            <select name="mail_driver" id="mail_driver"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                @foreach(['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($settings['email']['mail_driver'] ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('mail_driver') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="mail_host" class="block text-sm font-medium text-gray-700 mb-1">Mail Host</label>
                            <input type="text" name="mail_host" id="mail_host"
                                   value="{{ old('mail_host', $settings['email']['mail_host'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('mail_host') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="mail_port" class="block text-sm font-medium text-gray-700 mb-1">Mail Port</label>
                            <input type="number" name="mail_port" id="mail_port"
                                   value="{{ old('mail_port', $settings['email']['mail_port'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('mail_port') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="mail_username" class="block text-sm font-medium text-gray-700 mb-1">Mail Username</label>
                            <input type="text" name="mail_username" id="mail_username"
                                   value="{{ old('mail_username', $settings['email']['mail_username'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('mail_username') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="mail_password" class="block text-sm font-medium text-gray-700 mb-1">Mail Password</label>
                            <input type="password" name="mail_password" id="mail_password"
                                   value="{{ old('mail_password', $settings['email']['mail_password'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('mail_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="mail_encryption" class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                            <select name="mail_encryption" id="mail_encryption"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                @foreach(['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'None'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($settings['email']['mail_encryption'] ?? 'tls') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('mail_encryption') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="mail_from_address" class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                            <input type="email" name="mail_from_address" id="mail_from_address"
                                   value="{{ old('mail_from_address', $settings['email']['mail_from_address'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('mail_from_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="mail_from_name" class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                            <input type="text" name="mail_from_name" id="mail_from_name"
                                   value="{{ old('mail_from_name', $settings['email']['mail_from_name'] ?? '') }}"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            @error('mail_from_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">Save Email Settings</button>
                </div>
            </form>
        </div>

        {{-- Payment Settings Tab --}}
        <div x-show="activeTab === 'payment'" x-cloak>
            <form action="{{ route('admin.settings.payment') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="px-6 py-6 space-y-8">
                    {{-- Stripe --}}
                    <div class="rounded-lg border border-gray-200 p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-indigo-50 text-indigo-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </div>
                                <h4 class="text-base font-semibold text-gray-900">Stripe</h4>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="stripe_enabled" value="0">
                                <input type="checkbox" name="stripe_enabled" value="1" class="sr-only peer"
                                       {{ ($settings['payment']['stripe_enabled'] ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                            </label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="stripe_public_key" class="block text-sm font-medium text-gray-700 mb-1">Public Key</label>
                                <input type="text" name="stripe_public_key" id="stripe_public_key"
                                       value="{{ old('stripe_public_key', $settings['payment']['stripe_public_key'] ?? '') }}"
                                       class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            </div>
                            <div>
                                <label for="stripe_secret_key" class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                                <input type="password" name="stripe_secret_key" id="stripe_secret_key"
                                       value="{{ old('stripe_secret_key', $settings['payment']['stripe_secret_key'] ?? '') }}"
                                       class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    {{-- PayPal --}}
                    <div class="rounded-lg border border-gray-200 p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-50 text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <h4 class="text-base font-semibold text-gray-900">PayPal</h4>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="paypal_enabled" value="0">
                                <input type="checkbox" name="paypal_enabled" value="1" class="sr-only peer"
                                       {{ ($settings['payment']['paypal_enabled'] ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                            </label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="paypal_client_id" class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                                <input type="text" name="paypal_client_id" id="paypal_client_id"
                                       value="{{ old('paypal_client_id', $settings['payment']['paypal_client_id'] ?? '') }}"
                                       class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            </div>
                            <div>
                                <label for="paypal_client_secret" class="block text-sm font-medium text-gray-700 mb-1">Client Secret</label>
                                <input type="password" name="paypal_client_secret" id="paypal_client_secret"
                                       value="{{ old('paypal_client_secret', $settings['payment']['paypal_client_secret'] ?? '') }}"
                                       class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            </div>
                            <div>
                                <label for="paypal_mode" class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
                                <select name="paypal_mode" id="paypal_mode"
                                        class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                    <option value="sandbox" {{ ($settings['payment']['paypal_mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                    <option value="live" {{ ($settings['payment']['paypal_mode'] ?? '') == 'live' ? 'selected' : '' }}>Live</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Cash on Delivery --}}
                    <div class="rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-50 text-green-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                </div>
                                <h4 class="text-base font-semibold text-gray-900">Cash on Delivery</h4>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="cod_enabled" value="0">
                                <input type="checkbox" name="cod_enabled" value="1" class="sr-only peer"
                                       {{ ($settings['payment']['cod_enabled'] ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">Save Payment Settings</button>
                </div>
            </form>
        </div>

        {{-- Shipping Settings Tab --}}
        <div x-show="activeTab === 'shipping'" x-cloak>
            <form action="{{ route('admin.settings.shipping') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="free_shipping_threshold" class="block text-sm font-medium text-gray-700 mb-1">Free Shipping Threshold ($)</label>
                            <input type="number" name="free_shipping_threshold" id="free_shipping_threshold" step="0.01" min="0"
                                   value="{{ old('free_shipping_threshold', $settings['shipping']['free_shipping_threshold'] ?? '') }}"
                                   placeholder="e.g. 50.00"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            <p class="mt-1 text-xs text-gray-500">Orders above this amount qualify for free shipping. Leave empty to disable.</p>
                            @error('free_shipping_threshold') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="default_shipping_cost" class="block text-sm font-medium text-gray-700 mb-1">Default Shipping Cost ($)</label>
                            <input type="number" name="default_shipping_cost" id="default_shipping_cost" step="0.01" min="0"
                                   value="{{ old('default_shipping_cost', $settings['shipping']['default_shipping_cost'] ?? '') }}"
                                   placeholder="e.g. 5.99"
                                   class="block w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                            <p class="mt-1 text-xs text-gray-500">Flat rate applied when order total is below the free shipping threshold.</p>
                            @error('default_shipping_cost') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">Save Shipping Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection