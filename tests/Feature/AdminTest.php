<?php

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin login page loads', function () {
    $response = $this->get(route('admin.login'));
    $response->assertStatus(200);
});

test('admin can login with valid credentials', function () {
    $admin = Admin::factory()->create([
        'password' => bcrypt('admin123'),
        'role' => 'super_admin',
        'is_active' => true,
    ]);

    $response = $this->post(route('admin.login'), [
        'email' => $admin->email,
        'password' => 'admin123',
    ]);

    $response->assertRedirect();
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
