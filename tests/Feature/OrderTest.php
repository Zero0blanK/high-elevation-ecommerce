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

test('customer can return delivered order within one week', function () {
    $customer = Customer::factory()->create();
    $order = Order::factory()->delivered()->create([
        'customer_id' => $customer->id,
        'delivered_at' => now()->subDays(6),
    ]);

    $response = $this->actingAs($customer, 'customer')
        ->patch(route('orders.return', $order->id), [
            'return_reason' => 'The beans arrived stale.',
        ]);

    $response->assertRedirect();
    expect($order->fresh()->status)->toBe('delivered')
        ->and($order->fresh()->return_request_status)->toBe('pending')
        ->and($order->fresh()->return_reason)->toBe('The beans arrived stale.');
});

test('customer cannot return delivered order after one week', function () {
    $customer = Customer::factory()->create();
    $order = Order::factory()->delivered()->create([
        'customer_id' => $customer->id,
        'delivered_at' => now()->subDays(8),
    ]);

    $response = $this->actingAs($customer, 'customer')
        ->patch(route('orders.return', $order->id), [
            'return_reason' => 'Too late return request',
        ]);

    $response->assertSessionHasErrors('error');
    expect($order->fresh()->status)->toBe('delivered');
});

test('customer must provide reason when requesting return', function () {
    $customer = Customer::factory()->create();
    $order = Order::factory()->delivered()->create([
        'customer_id' => $customer->id,
        'delivered_at' => now()->subDays(2),
    ]);

    $response = $this->actingAs($customer, 'customer')
        ->patch(route('orders.return', $order->id), [
            'return_reason' => '',
        ]);

    $response->assertSessionHasErrors('return_reason');
    expect($order->fresh()->return_request_status)->toBeNull();
});
