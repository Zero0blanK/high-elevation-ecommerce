<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return auth('sanctum')->check();
    }

    public function rules()
    {
        return [
            'shipping_address' => 'required|array',
            'shipping_address.first_name' => 'required|string|max:100',
            'shipping_address.last_name' => 'required|string|max:100',
            'shipping_address.company' => 'nullable|string|max:100',
            'shipping_address.address_line_1' => 'required|string|max:255',
            'shipping_address.address_line_2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
            'shipping_address.phone' => 'nullable|string|max:20',
            
            'billing_address' => 'nullable|array',
            'billing_address.first_name' => 'required_with:billing_address|string|max:100',
            'billing_address.last_name' => 'required_with:billing_address|string|max:100',
            'billing_address.company' => 'nullable|string|max:100',
            'billing_address.address_line_1' => 'required_with:billing_address|string|max:255',
            'billing_address.address_line_2' => 'nullable|string|max:255',
            'billing_address.city' => 'required_with:billing_address|string|max:100',
            'billing_address.state' => 'required_with:billing_address|string|max:100',
            'billing_address.postal_code' => 'required_with:billing_address|string|max:20',
            'billing_address.country' => 'required_with:billing_address|string|max:100',
            'billing_address.phone' => 'nullable|string|max:20',
            
            'payment_method' => 'required|in:stripe',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'save_address' => 'boolean',
            'newsletter_signup' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'shipping_address.required' => 'Shipping address is required',
            'shipping_address.first_name.required' => 'Shipping first name is required',
            'shipping_address.last_name.required' => 'Shipping last name is required',
            'shipping_address.address_line_1.required' => 'Shipping street address is required',
            'shipping_address.city.required' => 'Shipping city is required',
            'shipping_address.state.required' => 'Shipping state/province is required',
            'shipping_address.postal_code.required' => 'Shipping postal code is required',
            'shipping_address.country.required' => 'Shipping country is required',
            'payment_method.required' => 'Payment method is required',
            'coupon_code.exists' => 'Invalid coupon code',
        ];
    }
}