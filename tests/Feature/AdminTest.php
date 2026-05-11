<?php

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('shared login page loads', function () {
    $response = $this->get(route('customer.login'));
    $response->assertStatus(200);
});

test('admin can login from shared login endpoint', function () {
    $admin = Admin::factory()->create([
        'password' => bcrypt('admin123'),
        'role' => 'super_admin',
        'is_active' => true,
    ]);

    $response = $this->post(route('customer.login'), [
        'email' => $admin->email,
        'password' => 'admin123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated('admin');
});

test('admin dashboard requires authentication', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect();
});

test('authenticated admin can access dashboard', function () {
    $admin = Admin::factory()->create([
        'role' => 'super_admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.dashboard'));

    $response->assertStatus(200);
});

test('customer cannot access admin dashboard', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'customer')
        ->get(route('admin.dashboard'));

    $response->assertStatus(403);
});
