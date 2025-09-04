<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize()
    {
        return auth('admin')->check() && auth('admin')->user()->isAdmin();
    }

    public function rules()
    {
        $couponId = $this->route('coupon')?->id;

        return [
            'code' => 'required|string|max:50|unique:coupons,code,' . $couponId,
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at'
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Coupon code is required',
            'code.unique' => 'This coupon code is already in use',
            'type.required' => 'Coupon type is required',
            'value.required' => 'Coupon value is required',
            'expires_at.after_or_equal' => 'Expiry date must be after or equal to start date',
        ];
    }
}