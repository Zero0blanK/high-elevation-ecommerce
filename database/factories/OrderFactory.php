<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 200);
        $tax = round($subtotal * 0.08, 2);
        $shipping = fake()->randomElement([5.99, 12.99, 0]);

        return [
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'customer_id' => Customer::factory(),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered']),
            'currency' => 'USD',
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'shipping_amount' => $shipping,
            'discount_amount' => 0,
            'total_amount' => $subtotal + $tax + $shipping,
            'payment_status' => fake()->randomElement(['pending', 'paid']),
            'payment_method' => 'stripe',
            'shipping_method' => fake()->randomElement(['standard', 'express', 'overnight']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn() => ['status' => 'pending', 'payment_status' => 'pending']);
    }

    public function paid(): static
    {
        return $this->state(fn() => ['status' => 'processing', 'payment_status' => 'paid']);
    }

    public function shipped(): static
    {
        return $this->state(fn() => [
            'status' => 'shipped',
            'payment_status' => 'paid',
            'tracking_number' => fake()->numerify('1Z#########'),
            'shipped_at' => now()->subDays(2),
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn() => [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'delivered_at' => now(),
        ]);
    }
}
