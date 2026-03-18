<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('??##??')),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['percentage', 'fixed_amount']),
            'value' => fake()->randomFloat(2, 5, 50),
            'minimum_amount' => fake()->randomFloat(2, 0, 50),
            'maximum_discount' => fake()->optional()->randomFloat(2, 10, 100),
            'usage_limit' => fake()->optional()->numberBetween(10, 1000),
            'usage_limit_per_customer' => fake()->optional()->numberBetween(1, 5),
            'used_count' => 0,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn() => ['expires_at' => now()->subDay()]);
    }

    public function percentage(): static
    {
        return $this->state(fn() => ['type' => 'percentage', 'value' => fake()->numberBetween(5, 30)]);
    }

    public function fixedAmount(): static
    {
        return $this->state(fn() => ['type' => 'fixed_amount', 'value' => fake()->numberBetween(5, 25)]);
    }
}
