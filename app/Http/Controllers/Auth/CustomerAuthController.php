<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$customer->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.'
            ]);
        }

        // Get session ID before login
        $sessionId = session()->getId();
        Auth::guard('customer')->login($customer, $request->boolean('remember'));

        // Update last login
        $customer->update(['last_login_at' => now()]);

        // Transfer guest cart to customer using CartService
        $this->cartService->mergeSessionCartToCustomer($customer, $sessionId);

        return redirect()->intended(route('home'));
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:customers',
                    function ($attribute, $value, $fail) {
                        $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];
                        $domain = explode('@', $value)[1] ?? '';
                        
                        if (!in_array(strtolower($domain), $allowedDomains)) {
                            $fail('Please use an email from: ' . implode(', ', $allowedDomains));
                        }
                    },
                ],
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:11',
                'date_of_birth' => 'nullable|date',
                'marketing_emails' => 'boolean'
            ]);

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

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('home'),
                    'message' => 'Registration successful!'
                ]);
            }

            return redirect()->route('home')->with('success', 'Registration successful!');

        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
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