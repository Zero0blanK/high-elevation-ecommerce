<?php

use App\Models\Order;
use App\Models\Customer;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can find order by number', function () {
    $order = Order::factory()->create();
    $orderService = app(OrderService::class);

    $found = $orderService->findOrderByNumber($order->order_number);

    expect($found)->not->toBeNull();
    expect($found->id)->toBe($order->id);
});

test('can find order by customer', function () {
    $customer = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer->id]);
    $orderService = app(OrderService::class);

    $found = $orderService->findOrderByCustomer($order->id, $customer->id);

    expect($found)->not->toBeNull();
    expect($found->customer_id)->toBe($customer->id);
});

test('pending order can be cancelled', function () {
    $order = Order::factory()->pending()->create();
    $orderService = app(OrderService::class);

    $orderService->cancelOrder($order);

    expect($order->fresh()->status)->toBe('cancelled');
});

test('shipped order cannot be cancelled', function () {
    $order = Order::factory()->shipped()->create();
    $orderService = app(OrderService::class);

    $this->expectException(\Exception::class);
    $orderService->cancelOrder($order);
});

test('order counts by status are correct', function () {
    $customer = Customer::factory()->create();
    Order::factory()->pending()->count(2)->create(['customer_id' => $customer->id]);
    Order::factory()->shipped()->count(3)->create(['customer_id' => $customer->id]);

    $orderService = app(OrderService::class);
    $counts = $orderService->getOrderCountsByStatus($customer->id);

    expect($counts['all'])->toBe(5);
    expect($counts['pending'])->toBe(2);
    expect($counts['shipped'])->toBe(3);
});
