<?php

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access checkout', function () {
    $response = $this->get(route('checkout.index'));
    $response->assertRedirect();
});

test('authenticated customer can access checkout', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'customer')
        ->get(route('checkout.index'));

    $response->assertStatus(200);
});

test('guest cannot access orders', function () {
    $response = $this->get(route('orders.index'));
    $response->assertRedirect();
});

test('customer can view their orders', function () {
    $customer = Customer::factory()->create();
    Order::factory()->count(3)->create(['customer_id' => $customer->id]);

    $response = $this->actingAs($customer, 'customer')
        ->get(route('orders.index'));

    $response->assertStatus(200);
});

test('customer can view order details', function () {
    $customer = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer->id]);

    $response = $this->actingAs($customer, 'customer')
        ->get(route('orders.show', $order->id));

    $response->assertStatus(200);
});
