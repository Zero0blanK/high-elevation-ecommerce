<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Customer;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function showLoginForm()
    {
        return view('auth.customer.login');
    }

    public function showRegistrationForm()
    {
        return view('auth.customer.register');
    }

    public function login(Request $request)
    {
        $isAjax = $request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json';
        
        // Validate with custom error handling for AJAX
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the errors below.',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            if (!$admin->is_active) {
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account has been deactivated.',
                        'errors' => ['email' => ['Your account has been deactivated. Please contact support.']]
                    ], 422);
                }

                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact support.'
                ]);
            }

            Auth::guard('customer')->logout();
            Auth::guard('admin')->login($admin, $request->boolean('remember'));
            $admin->update(['last_login_at' => now()]);

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('admin.dashboard'),
                    'message' => 'Login successful!'
                ]);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided credentials are incorrect.',
                    'errors' => ['email' => ['The provided credentials are incorrect.']]
                ], 422);
            }
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$customer->is_active) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated.',
                    'errors' => ['email' => ['Your account has been deactivated. Please contact support.']]
                ], 422);
            }
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.'
            ]);
        }

        // Get session ID before login
        $sessionId = session()->getId();
        Auth::guard('admin')->logout();
        Auth::guard('customer')->login($customer, $request->boolean('remember'));

        // Update last login
        $customer->update(['last_login_at' => now()]);

        // Transfer guest cart to customer using CartService
        $this->cartService->mergeSessionCartToCustomer($customer, $sessionId);

        if ($isAjax) {
            return response()->json([
                'success' => true,
                'redirect' => route('home'),
                'message' => 'Login successful!'
            ]);
        }

        return redirect()->intended(route('home'));
    }

    public function register(Request $request)
    {
        $isAjax = $request->expectsJson() || $request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json';
        
        // Validate with custom error handling for AJAX
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                'unique:customers',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\#^()_+=\[\]{}|\\:";\'<>,.\/~`-])[A-Za-z\d@$!%*?&\#^()_+=\[\]{}|\\:";\'<>,.\/~`-]{8,}$/',
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^\+?[1-9]\d{1,14}$/', // E.164 international phone format
            ],
            'date_of_birth' => 'nullable|date|before:today',
            'marketing_emails' => 'boolean'
        ], [
            'email.regex' => 'Please enter a valid email address.',
            'password.regex' => 'Password must contain at least 1 uppercase, 1 lowercase, 1 number, and 1 special character.',
            'password.min' => 'Password must be at least 8 characters.',
            'phone.regex' => 'Please enter a valid phone number (e.g., +639123456789 or 09123456789).',
        ]);

        if ($validator->fails()) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the errors below.',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $customer = Customer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'is_active' => true,
            ]);

            Auth::guard('customer')->login($customer);

            // Transfer guest cart to customer using CartService
            $this->cartService->mergeSessionCartToCustomer($customer);

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('home'),
                    'message' => 'Registration successful!'
                ]);
            }

            return redirect()->route('home')->with('success', 'Registration successful!');

        } catch (\Exception $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred. Please try again.',
                    'errors' => ['general' => ['An unexpected error occurred. Please try again.']]
                ], 500);
            }
            throw $e;
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
