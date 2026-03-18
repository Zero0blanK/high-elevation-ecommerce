<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductImageSeeder::class,
            SettingsSeeder::class,
            CurrencySeeder::class,
            CustomerSeeder::class,
            CouponSeeder::class,
        ]);
    }
}