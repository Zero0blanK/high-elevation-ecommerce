<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $customerId = auth('sanctum')->id();

        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:customers,email,' . $customerId,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'preferences' => 'nullable|array',
            'preferences.preferred_roast_level' => 'nullable|string|in:light,medium,dark,extra_dark',
            'preferences.preferred_grind_type' => 'nullable|string|in:whole_bean,coarse,medium,fine,extra_fine',
            'preferences.favorite_origins' => 'nullable|string',
            'preferences.marketing_emails' => 'boolean',
            'preferences.order_notifications' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email address is required',
            'email.unique' => 'This email address is already in use',
            'date_of_birth.before' => 'Date of birth must be in the past',
        ];
    }
}