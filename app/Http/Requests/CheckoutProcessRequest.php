<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guard('customer')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shipping_address_id' => 'required|exists:customer_addresses,id',
            'payment_method' => 'nullable|string',
            'same_as_shipping' => 'nullable|boolean',
            'billing_address_id' => 'nullable|exists:customer_addresses,id',
            'order_notes' => 'nullable|string|max:1000',
            'paypal_order_id' => 'nullable|string',
        ];
    }
}
