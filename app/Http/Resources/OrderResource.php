<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'currency' => $this->currency,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'shipping_amount' => $this->shipping_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'shipping_method' => $this->shipping_method,
            'tracking_number' => $this->tracking_number,
            'notes' => $this->notes,
            'items' => $this->items?->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_sku' => $item->product_sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'product_options' => $item->product_options,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'primary_image' => $item->product->primaryImage ? [
                            'url' => $item->product->primaryImage->image_url,
                            'alt' => $item->product->primaryImage->alt_text,
                        ] : null,
                    ] : null,
                ];
            }),
            'addresses' => $this->addresses?->map(function ($address) {
                return [
                    'type' => $address->type,
                    'first_name' => $address->first_name,
                    'last_name' => $address->last_name,
                    'company' => $address->company,
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                    'phone' => $address->phone,
                ];
            }),
            'customer' => $this->customer ? [
                'id' => $this->customer->id,
                'name' => $this->customer->full_name,
                'email' => $this->customer->email,
            ] : null,
            'timestamps' => [
                'created_at' => $this->created_at,
                'shipped_at' => $this->shipped_at,
                'delivered_at' => $this->delivered_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
