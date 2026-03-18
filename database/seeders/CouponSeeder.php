<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'description' => '10% off for new customers',
                'type' => 'percentage',
                'value' => 10.00,
                'minimum_amount' => 25.00,
                'maximum_discount' => 50.00,
                'usage_limit' => 1000,
                'usage_limit_per_customer' => 1,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'COFFEE20'],
            [
                'description' => '$20 off orders over $100',
                'type' => 'fixed_amount',
                'value' => 20.00,
                'minimum_amount' => 100.00,
                'usage_limit' => 500,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'FREESHIP'],
            [
                'description' => 'Free shipping on any order',
                'type' => 'fixed_amount',
                'value' => 5.99,
                'minimum_amount' => 0,
                'usage_limit' => null,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
            ]
        );
    }
}
