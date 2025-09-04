<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'general' => $this->getSettingsGroup('general'),
            'email' => $this->getSettingsGroup('email'),
            'payment' => $this->getSettingsGroup('payment'),
            'shipping' => $this->getSettingsGroup('shipping'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_email' => 'required|email',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:500',
            'timezone' => 'required|string',
            'currency' => 'required|string|max:3',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:512',
        ]);

        $settings = [
            'site_name' => $request->site_name,
            'site_description' => $request->site_description,
            'site_email' => $request->site_email,
            'site_phone' => $request->site_phone,
            'site_address' => $request->site_address,
            'timezone' => $request->timezone,
            'currency' => $request->currency,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('settings', 'public');
            $settings['logo'] = $logoPath;
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('settings', 'public');
            $settings['favicon'] = $faviconPath;
        }

        $this->updateSettings('general', $settings);

        return back()->with('success', 'General settings updated successfully.');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|in:smtp,sendmail,mailgun,ses',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|integer',
            'mail_username' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_password' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        $settings = [
            'mail_driver' => $request->mail_driver,
            'mail_host' => $request->mail_host,
            'mail_port' => $request->mail_port,
            'mail_username' => $request->mail_username,
            'mail_encryption' => $request->mail_encryption,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_name' => $request->mail_from_name,
        ];

        // Only update password if provided
        if ($request->filled('mail_password')) {
            $settings['mail_password'] = $request->mail_password;
        }

        $this->updateSettings('email', $settings);

        return back()->with('success', 'Email settings updated successfully.');
    }

    public function updatePayment(Request $request)
    {
        $request->validate([
            'stripe_enabled' => 'boolean',
            'stripe_public_key' => 'required_if:stripe_enabled,1|nullable|string',
            'stripe_secret_key' => 'required_if:stripe_enabled,1|nullable|string',
            'paypal_enabled' => 'boolean',
            'paypal_client_id' => 'required_if:paypal_enabled,1|nullable|string',
            'paypal_client_secret' => 'required_if:paypal_enabled,1|nullable|string',
            'paypal_mode' => 'required_if:paypal_enabled,1|nullable|in:sandbox,live',
            'cod_enabled' => 'boolean',
        ]);

        $settings = [
            'stripe_enabled' => $request->boolean('stripe_enabled'),
            'stripe_public_key' => $request->stripe_public_key,
            'stripe_secret_key' => $request->stripe_secret_key,
            'paypal_enabled' => $request->boolean('paypal_enabled'),
            'paypal_client_id' => $request->paypal_client_id,
            'paypal_client_secret' => $request->paypal_client_secret,
            'paypal_mode' => $request->paypal_mode,
            'cod_enabled' => $request->boolean('cod_enabled'),
        ];

        $this->updateSettings('payment', $settings);

        return back()->with('success', 'Payment settings updated successfully.');
    }

    public function updateShipping(Request $request)
    {
        $request->validate([
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'default_shipping_cost' => 'required|numeric|min:0',
            'shipping_zones' => 'nullable|array',
            'shipping_zones.*.name' => 'required|string|max:255',
            'shipping_zones.*.cost' => 'required|numeric|min:0',
            'shipping_zones.*.countries' => 'required|array',
        ]);

        $settings = [
            'free_shipping_threshold' => $request->free_shipping_threshold,
            'default_shipping_cost' => $request->default_shipping_cost,
            'shipping_zones' => $request->shipping_zones ?? [],
        ];

        $this->updateSettings('shipping', $settings);

        return back()->with('success', 'Shipping settings updated successfully.');
    }

    private function getSettingsGroup($group)
    {
        return Setting::where('group', $group)->pluck('value', 'key')->toArray();
    }

    private function updateSettings($group, $settings)
    {
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['group' => $group, 'key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );
        }

        // Clear settings cache
        Cache::forget('settings_' . $group);
    }
}