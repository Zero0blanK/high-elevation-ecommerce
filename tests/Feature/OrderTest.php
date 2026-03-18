<?php

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer can cancel pending order', function () {
    $customer = Customer::factory()->create();
    $order = Order::factory()->pending()->create(['customer_id' => $customer->id]);

    $response = $this->actingAs($customer, 'customer')
        ->patch(route('orders.cancel', $order->id));

    $response->assertRedirect();
    expect($order->fresh()->status)->toBe('cancelled');
});

test('customer cannot cancel shipped order', function () {
    $customer = Customer::factory()->create();
    $order = Order::factory()->shipped()->create(['customer_id' => $customer->id]);

    $response = $this->actingAs($customer, 'customer')
        ->patch(route('orders.cancel', $order->id));

    expect($order->fresh()->status)->toBe('shipped');
});

test('customer cannot view another customers order', function () {
    $customer1 = Customer::factory()->create();
    $customer2 = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer1->id]);

    $response = $this->actingAs($customer2, 'customer')
        ->get(route('orders.show', $order->id));

    $response->assertStatus(403)->or($response->assertStatus(404));
});
