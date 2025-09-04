<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $singleOrigin = Category::where('slug', 'single-origin')->first();
        $blends = Category::where('slug', 'blends')->first();
        $decaf = Category::where('slug', 'decaf')->first();
        $espresso = Category::where('slug', 'espresso')->first();
        $coldBrew = Category::where('slug', 'cold-brew')->first();

        $products = [
            // Single Origin
            [
                'name' => 'Ethiopian Yirgacheffe',
                'slug' => 'ethiopian-yirgacheffe',
                'short_description' => 'Bright, floral notes with wine-like acidity and citrus undertones.',
                'description' => 'Our Ethiopian Yirgacheffe is sourced from high-altitude farms in the Gedeo Zone. This coffee showcases the classic Ethiopian profile with bright acidity, floral aromatics, and complex fruit notes. The beans are washed processed, highlighting the clean, tea-like body and wine-like characteristics that make Yirgacheffe coffees so distinctive.',
                'price' => 24.99,
                'sale_price' => null,
                'category_id' => $singleOrigin->id,
                'sku' => 'ETH-YIR-001',
                'stock_quantity' => 50,
                'weight' => 1.0,
                'roast_level' => 'light',
                'grind_type' => 'whole_bean',
                'origin' => 'Ethiopia',
                'is_active' => true,
                'is_featured' => true,
                'meta_title' => 'Ethiopian Yirgacheffe Coffee Beans - Light Roast',
                'meta_description' => 'Premium Ethiopian Yirgacheffe coffee beans with bright, floral notes and wine-like acidity. Perfect for pour-over brewing.',
            ],
            [
                'name' => 'Colombian Supremo',
                'slug' => 'colombian-supremo',
                'short_description' => 'Well-balanced with caramel sweetness and nutty finish.',
                'description' => 'Colombian Supremo represents the highest grade of Colombian coffee beans. Grown in the high altitudes of the Andes Mountains, these beans offer a perfect balance of sweetness and acidity with notes of caramel, chocolate, and nuts. The medium body and clean finish make this an excellent everyday coffee.',
                'price' => 22.99,
                'sale_price' => 19.99,
                'category_id' => $singleOrigin->id,
                'sku' => 'COL-SUP-001',
                'stock_quantity' => 75,
                'weight' => 1.0,
                'roast_level' => 'medium',
                'grind_type' => 'whole_bean',
                'origin' => 'Colombia',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Guatemala Antigua',
                'slug' => 'guatemala-antigua',
                'short_description' => 'Full-bodied with smoky, spicy notes and chocolate undertones.',
                'description' => 'Grown in the volcanic soil of the Antigua valley, this Guatemalan coffee offers a distinctive smoky flavor profile with spicy notes and rich chocolate undertones. The high altitude and volcanic minerals create a complex, full-bodied coffee with excellent depth and character.',
                'price' => 26.99,
                'sale_price' => null,
                'category_id' => $singleOrigin->id,
                'sku' => 'GUA-ANT-001',
                'stock_quantity' => 30,
                'weight' => 1.0,
                'roast_level' => 'medium_dark',
                'grind_type' => 'whole_bean',
                'origin' => 'Guatemala',
                'is_active' => true,
                'is_featured' => false,
            ],

            // Blends
            [
                'name' => 'House Blend',
                'slug' => 'house-blend',
                'short_description' => 'Our signature blend with balanced flavor and smooth finish.',
                'description' => 'Our carefully crafted House Blend combines beans from Central and South America to create a perfectly balanced coffee with medium body, subtle sweetness, and a smooth finish. This versatile blend works well with any brewing method and is perfect for daily enjoyment.',
                'price' => 19.99,
                'sale_price' => 16.99,
                'category_id' => $blends->id,
                'sku' => 'HOU-BLE-001',
                'stock_quantity' => 100,
                'weight' => 1.0,
                'roast_level' => 'medium',
                'grind_type' => 'whole_bean',
                'origin' => 'Blend',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Breakfast Blend',
                'slug' => 'breakfast-blend',
                'short_description' => 'Bright and crisp with floral notes. Perfect morning coffee.',
                'description' => 'Start your day right with our Breakfast Blend, a light to medium roast that combines bright Central American beans with smooth South American varieties. This blend offers a clean, crisp flavor with subtle floral notes and gentle acidity that pairs perfectly with your morning routine.',
                'price' => 18.99,
                'sale_price' => null,
                'category_id' => $blends->id,
                'sku' => 'BRE-BLE-001',
                'stock_quantity' => 80,
                'weight' => 1.0,
                'roast_level' => 'light',
                'grind_type' => 'whole_bean',
                'origin' => 'Blend',
                'is_active' => true,
                'is_featured' => true,
            ],

            // Espresso
            [
                'name' => 'Espresso Roast',
                'slug' => 'espresso-roast',
                'short_description' => 'Bold and rich with thick crema. Perfect for espresso shots.',
                'description' => 'Our Espresso Roast is specifically blended and roasted to create the perfect espresso shot. This dark roast blend produces a rich, full-bodied coffee with low acidity and a thick, golden crema. The bold flavor profile includes notes of dark chocolate and caramel.',
                'price' => 21.99,
                'sale_price' => null,
                'category_id' => $espresso->id,
                'sku' => 'ESP-ROA-001',
                'stock_quantity' => 60,
                'weight' => 1.0,
                'roast_level' => 'dark',
                'grind_type' => 'whole_bean',
                'origin' => 'Blend',
                'is_active' => true,
                'is_featured' => true,
            ],
            // Decaf
            [
                'name' => 'Swiss Water Decaf',
                'slug' => 'swiss-water-decaf',
                'short_description' => 'Full flavor without caffeine using the Swiss Water process.',
                'description' => "Enjoy great coffee flavor any time of day with our Swiss Water Decaf. Using the chemical-free Swiss Water process, we remove 99.9% of caffeine while preserving the coffee's original flavor characteristics. This medium roast offers notes of chocolate and nuts with a smooth, satisfying finish.",
                'price' => 23.99,
                'sale_price' => null,
                'category_id' => $decaf->id,
                'sku' => 'SWI-DEC-001',
                'stock_quantity' => 40,
                'weight' => 1.0,
                'roast_level' => 'medium',
                'grind_type' => 'whole_bean',
                'origin' => 'Colombia',
                'is_active' => true,
                'is_featured' => false,
            ],

            // Cold Brew
            [
                'name' => 'Cold Brew Blend',
                'slug' => 'cold-brew-blend',
                'short_description' => 'Smooth, low-acid coffee perfect for cold brewing.',
                'description' => "Specially crafted for cold brewing, this blend combines beans selected for their low acidity and smooth flavor profile. The result is a concentrate that's naturally sweet with notes of chocolate and caramel, perfect for iced coffee drinks or cold brew concentrate.",
                'price' => 20.99,
                'sale_price' => 17.99,
                'category_id' => $coldBrew->id,
                'sku' => 'COL-BRE-001',
                'stock_quantity' => 45,
                'weight' => 1.0,
                'roast_level' => 'medium_dark',
                'grind_type' => 'coarse',
                'origin' => 'Blend',
                'is_active' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
