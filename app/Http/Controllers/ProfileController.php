<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

        try {
            $validated = $request->validate([
                'first_name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[A-Za-z\s-]+$/',
                ],
                'last_name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[A-Za-z\s-]+$/',
                ],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:customers,email,' . $customer->id,
                ],
                'phone' => [
                    'nullable',
                    'string',
                    'regex:/^[0-9]+$/',
                    'min:10',
                    'max:15',
                ],
                'date_of_birth' => [
                    'nullable',
                    'date',
                    'before:today',
                ],
            ], [
                'first_name.regex' => 'First name can only contain letters, spaces, and hyphens.',
                'last_name.regex' => 'Last name can only contain letters, spaces, and hyphens.',
                'phone.regex' => 'Phone number can only contain numbers.',
                'date_of_birth.before' => 'Date of birth must be in the past.',
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!'
                ]);
            }

            return redirect()->route('account.profile')->with('success', 'Profile updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
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
        try {
            $validated = $request->validate([
                'current_password' => [
                    'required',
                    'string',
                    'min:8'
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'different:current_password',
                ],
                'password_confirmation' => [
                    'required',
                    'string',
                    'min:8'
                ]
            ], [
                'current_password.required' => 'Please enter your current password.',
                'current_password.min' => 'Current password must be at least 8 characters.',
                'password.required' => 'Please enter a new password.',
                'password.min' => 'New password must be at least 8 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
                'password.different' => 'New password must be different from your current password.',
                'password_confirmation.required' => 'Please confirm your new password.',
                'password_confirmation.min' => 'Password confirmation must be at least 8 characters.'
            ]);

            $customer = Auth::guard('customer')->user();

            if (!Hash::check($validated['current_password'], $customer->password)) {
                $errorMessage = 'The current password is incorrect.';
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'current_password' => [$errorMessage]
                        ]
                    ], 422);
                }
                return back()->withErrors(['current_password' => $errorMessage]);
            }

            // Check if new password is same as current password
            if (Hash::check($validated['password'], $customer->password)) {
                $errorMessage = 'New password cannot be the same as your current password.';
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'password' => [$errorMessage]
                        ]
                    ], 422);
                }
                return back()->withErrors(['password' => $errorMessage]);
            }

            try {
                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update([
                        'password' => Hash::make($validated['password']),
                        'updated_at' => now()
                    ]);

                $successMessage = 'Password updated successfully!';
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMessage
                    ]);
                }
                return back()->with('success', $successMessage);

            } catch (\Exception $e) {
                Log::error('Password update failed: ' . $e->getMessage());
                $errorMessage = 'Failed to update password. Please try again.';
                
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['general' => [$errorMessage]]
                    ], 500);
                }
                return back()->withErrors(['general' => $errorMessage]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error during password update: ' . $e->getMessage());
            $errorMessage = 'An unexpected error occurred. Please try again.';
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => [$errorMessage]]
                ], 500);
            }
            return back()->withErrors(['general' => $errorMessage]);
        }
    }

    public function getUserAddress(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $addressId = $request->input('address_id');

        $address = CustomerAddress::where('customer_id', $customer->id)
            ->where('id', $addressId)
            ->first();

        if ($address) {
            return response()->json([
                'success' => true,
                'address' => $address
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.'
            ], 404);
        }
    }
}