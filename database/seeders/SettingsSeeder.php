<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General Settings
            ['group' => 'general', 'key' => 'site_name', 'value' => 'High Elevation'],
            ['group' => 'general', 'key' => 'site_email', 'value' => 'info@highelevation.com'],
            ['group' => 'general', 'key' => 'site_phone', 'value' => '+1 (555) 123-4567'],
            ['group' => 'general', 'key' => 'currency', 'value' => 'USD'],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'Premium outdoor gear and equipment'],

            // Email Settings
            ['group' => 'email', 'key' => 'smtp_host', 'value' => 'smtp.gmail.com'],
            ['group' => 'email', 'key' => 'smtp_port', 'value' => '587'],
            ['group' => 'email', 'key' => 'smtp_username', 'value' => ''],
            ['group' => 'email', 'key' => 'smtp_password', 'value' => ''],
            ['group' => 'email', 'key' => 'smtp_encryption', 'value' => 'tls'],

            // Payment Settings
            ['group' => 'payment', 'key' => 'stripe_public_key', 'value' => ''],
            ['group' => 'payment', 'key' => 'stripe_secret_key', 'value' => ''],
            ['group' => 'payment', 'key' => 'paypal_client_id', 'value' => ''],
            ['group' => 'payment', 'key' => 'paypal_secret', 'value' => ''],

            // Shipping Settings
            ['group' => 'shipping', 'key' => 'free_shipping_threshold', 'value' => '100'],
            ['group' => 'shipping', 'key' => 'standard_shipping_rate', 'value' => '9.99'],
            ['group' => 'shipping', 'key' => 'express_shipping_rate', 'value' => '19.99'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}