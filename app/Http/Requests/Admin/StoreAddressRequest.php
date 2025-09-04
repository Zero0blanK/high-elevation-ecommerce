<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize()
    {
        return auth('sanctum')->check();
    }

    public function rules()
    {
        return [
            'type' => 'required|in:billing,shipping',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'company' => 'nullable|string|max:100',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'Address type is required',
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'address_line_1.required' => 'Street address is required',
            'city.required' => 'City is required',
            'state.required' => 'State/Province is required',
            'postal_code.required' => 'Postal code is required',
            'country.required' => 'Country is required',
        ];
    }
}