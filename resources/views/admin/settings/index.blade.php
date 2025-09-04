@extends('admin.layouts.app')

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