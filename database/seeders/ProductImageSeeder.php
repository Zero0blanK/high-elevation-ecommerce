<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Create primary image
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => "/images/products/{$product->slug}-1.jpg",
                'alt_text' => $product->name . ' - Main Image',
                'sort_order' => 1,
                'is_primary' => true,
            ]);

            // Create additional images
            for ($i = 2; $i <= 3; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => "/images/products/{$product->slug}-{$i}.jpg",
                    'alt_text' => $product->name . " - Image {$i}",
                    'sort_order' => $i,
                    'is_primary' => false,
                ]);
            }
        }
    }
}
