<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true) . ' Coffee';
        $price = fake()->randomFloat(2, 12, 35);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 9999),
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(10),
            'sku' => 'HE-' . strtoupper(Str::random(8)),
            'price' => $price,
            'sale_price' => fake()->boolean(30) ? round($price * 0.85, 2) : null,
            'category_id' => Category::inRandomOrder()->first()?->id ?? CategoryFactory::new(),
            'stock_quantity' => fake()->numberBetween(0, 200),
            'low_stock_threshold' => 10,
            'weight' => fake()->randomFloat(2, 0.25, 2.0),
            'roast_level' => fake()->randomElement(['light', 'medium', 'medium_dark', 'dark']),
            'grind_type' => fake()->randomElement(['whole_bean', 'coarse', 'medium', 'fine', 'extra_fine']),
            'origin' => fake()->randomElement(['Ethiopia', 'Colombia', 'Brazil', 'Guatemala', 'Kenya', 'Costa Rica', 'Indonesia']),
            'is_featured' => fake()->boolean(20),
            'is_active' => true,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn() => ['stock_quantity' => 0]);
    }

    public function featured(): static
    {
        return $this->state(fn() => ['is_featured' => true]);
    }

    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            return ['sale_price' => round($attributes['price'] * 0.8, 2)];
        });
    }
}
