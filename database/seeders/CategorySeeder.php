<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Single Origin',
                'slug' => 'single-origin',
                'description' => 'Premium single-origin coffee beans from specific regions around the world, showcasing unique terroir and flavor profiles.',
                'image_url' => '/images/category-single-origin.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Blends',
                'slug' => 'blends',
                'description' => 'Expertly crafted coffee blends combining beans from multiple origins for the perfect balance of flavor, body, and aroma.',
                'image_url' => '/images/category-blends.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Decaf',
                'slug' => 'decaf',
                'description' => 'Full-flavored decaffeinated coffee beans processed using the Swiss Water method, perfect for any time of day.',
                'image_url' => '/images/category-decaf.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Espresso',
                'slug' => 'espresso',
                'description' => 'Dark roasted beans specifically selected and roasted for espresso brewing, delivering rich crema and bold flavor.',
                'image_url' => '/images/category-espresso.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Cold Brew',
                'slug' => 'cold-brew',
                'description' => 'Specially selected and roasted beans perfect for cold brewing, offering smooth, low-acid coffee concentrate.',
                'image_url' => '/images/category-cold-brew.jpg',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
