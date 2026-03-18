<?php

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer login page loads', function () {
    $response = $this->get(route('customer.login'));
    $response->assertStatus(200);
});

test('customer register page loads', function () {
    $response = $this->get(route('customer.register'));
    $response->assertStatus(200);
});

test('customer can register', function () {
    $response = $this->post(route('customer.register'), [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('customers', ['email' => 'test@example.com']);
});

test('customer can login with valid credentials', function () {
    $customer = Customer::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post(route('customer.login'), [
        'email' => $customer->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect();
    $this->assertAuthenticatedAs($customer, 'customer');
});

test('customer cannot login with invalid credentials', function () {
    $customer = Customer::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post(route('customer.login'), [
        'email' => $customer->email,
        'password' => 'wrongpassword',
    ]);

    $this->assertGuest('customer');
});

test('customer can logout', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'customer')
        ->post(route('customer.logout'));

    $this->assertGuest('customer');
});
