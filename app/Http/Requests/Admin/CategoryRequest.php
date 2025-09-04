<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth('admin')->check() && auth('admin')->user()->canManageProducts();
    }

    public function rules()
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Category name is required',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be in JPEG, PNG, JPG, or WebP format',
            'image.max' => 'Image must not exceed 2MB',
        ];
    }
}