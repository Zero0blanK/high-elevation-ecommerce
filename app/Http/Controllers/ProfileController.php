<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\CustomerAddress;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        $recentOrders = $customer->orders()->latest()->take(5)->get();
        $totalOrders = $customer->orders()->count();
        $totalSpent = $customer->orders()->where('payment_status', 'paid')->sum('total_amount');
        
        return view('account.dashboard', compact('customer', 'recentOrders', 'totalOrders', 'totalSpent'));
    }

    public function profile()
    {
        $customer = Auth::guard('customer')->user();
        return view('account.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
        ]);

        $customer->update($request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'date_of_birth'
        ]));

        return redirect()->route('account.profile')->with('success', 'Profile updated successfully!');
    }

    public function addresses()
    {
        $customer = Auth::guard('customer')->user();
        $addresses = $customer->addresses()->get();
        
        return view('account.addresses', compact('customer', 'addresses'));
    }

    public function storeAddress(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validatedData = $request->validate([
            'type' => 'required|in:billing,shipping,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'is_default' => 'nullable|boolean'
        ]);

        $isFirstAddress = $customer->addresses()->count() === 0;
        $isDefault = $isFirstAddress || $request->boolean('is_default', false);

        if ($isDefault) {
            CustomerAddress::where('customer_id', $customer->id)->update(['is_default' => false]);
        }

        try {
            $address = new CustomerAddress($validatedData);
            $address->customer_id = $customer->id;
            $address->is_default = $isDefault;
            $address->save();

            return redirect()->route('account.addresses')
                ->with('success', 'Address added successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to save address: ' . $e->getMessage());
        }
    }

    public function updateAddress(Request $request, CustomerAddress $address)
    {
        $customer = Auth::guard('customer')->user();

        // Ensure the address belongs to the authenticated customer
        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:billing,shipping,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'is_default' => 'boolean'
        ]);

        // If this is set as default, remove default from other addresses
        if ($request->is_default) {
            $customer->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($request->all());

        return redirect()->route('account.addresses')->with('success', 'Address updated successfully!');
    }

    public function destroyAddress(CustomerAddress $address)
    {
        $customer = Auth::guard('customer')->user();

        // Ensure the address belongs to the authenticated customer
        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('account.addresses')->with('success', 'Address deleted successfully!');
    }

    public function preferences()
    {
        $customer = Auth::guard('customer')->user();
        return view('account.preferences', compact('customer'));
    }

    public function updatePreferences(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'order_updates' => 'boolean',
            'newsletter' => 'boolean',
            'preferred_contact_method' => 'in:email,sms,phone',
            'timezone' => 'string|max:50'
        ]);

        $customer->update($request->only([
            'email_notifications',
            'sms_notifications', 
            'marketing_emails',
            'order_updates',
            'newsletter',
            'preferred_contact_method',
            'timezone'
        ]));

        return redirect()->route('account.preferences')->with('success', 'Preferences updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $customer = Auth::guard('customer')->user();

        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $customer->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}