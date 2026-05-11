<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guard('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'tracking_number' => 'required_if:status,shipped,delivered|nullable|string|max:100',
            'shipping_method' => 'required_if:status,shipped,delivered|nullable|string|max:100',
            'notes' => 'nullable|string',
            'quick_deliver' => 'nullable|boolean',
        ];
    }
}
