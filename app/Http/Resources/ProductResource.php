<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'sku' => $this->sku,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'discounted_price' => $this->discounted_price,
            'is_on_sale' => $this->isOnSale(),
            'stock_quantity' => $this->stock_quantity,
            'is_in_stock' => $this->isInStock(),
            'weight' => $this->weight,
            'roast_level' => $this->roast_level,
            'grind_type' => $this->grind_type,
            'origin' => $this->origin,
            'is_featured' => $this->is_featured,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'images' => $this->images?->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'alt' => $image->alt_text,
                    'is_primary' => $image->is_primary,
                ];
            }),
            'primary_image' => $this->primaryImage ? [
                'id' => $this->primaryImage->id,
                'url' => $this->primaryImage->image_url,
                'alt' => $this->primaryImage->alt_text,
            ] : null,
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
