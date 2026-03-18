<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0100',
                'is_active' => true,
            ]
        );

        CustomerAddress::firstOrCreate(
            ['customer_id' => $customer->id, 'type' => 'shipping', 'is_default' => true],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'address_line_1' => '123 Coffee Street',
                'city' => 'Seattle',
                'state' => 'WA',
                'postal_code' => '98101',
                'country' => 'US',
            ]
        );

        CustomerPreference::firstOrCreate(
            ['customer_id' => $customer->id],
            [
                'preferred_roast_level' => 'medium',
                'preferred_grind_type' => 'whole_bean',
                'marketing_emails' => true,
                'order_notifications' => true,
            ]
        );

        $customer2 = Customer::firstOrCreate(
            ['email' => 'jane@example.com'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0200',
                'is_active' => true,
            ]
        );

        CustomerAddress::firstOrCreate(
            ['customer_id' => $customer2->id, 'type' => 'shipping', 'is_default' => true],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'address_line_1' => '456 Bean Avenue',
                'city' => 'Portland',
                'state' => 'OR',
                'postal_code' => '97201',
                'country' => 'US',
            ]
        );
    }
}
