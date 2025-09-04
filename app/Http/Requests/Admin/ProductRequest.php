<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return auth('admin')->check() && auth('admin')->user()->canManageProducts();
    }

    public function rules()
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'required|string|max:100|unique:products,sku,' . $productId,
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'category_id' => 'nullable|exists:categories,id',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'roast_level' => 'required|in:light,medium,dark,extra_dark',
            'grind_type' => 'required|in:whole_bean,coarse,medium,fine,extra_fine',
            'origin' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required',
            'sku.required' => 'SKU is required',
            'sku.unique' => 'This SKU is already in use',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a valid number',
            'sale_price.lt' => 'Sale price must be less than regular price',
            'stock_quantity.required' => 'Stock quantity is required',
            'weight.required' => 'Weight is required',
            'images.*.image' => 'Each file must be an image',
            'images.*.mimes' => 'Images must be in JPEG, PNG, JPG, or WebP format',
            'images.*.max' => 'Each image must not exceed 2MB',
        ];
    }
}