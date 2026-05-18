<?php

use App\Models\Admin;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can approve pending return request', function () {
    $admin = Admin::factory()->create([
        'is_active' => true,
    ]);

    $order = Order::factory()->delivered()->create([
        'return_request_status' => 'pending',
        'return_reason' => 'Wrong product received.',
        'return_requested_at' => now()->subHour(),
    ]);

    $response = $this->actingAs($admin, 'admin')
        ->patch(route('admin.orders.return.approve', $order));

    $response->assertRedirect();
    expect($order->fresh()->status)->toBe('refunded')
        ->and($order->fresh()->return_request_status)->toBe('approved');
});

test('admin can deny pending return request', function () {
    $admin = Admin::factory()->create([
        'is_active' => true,
    ]);

    $order = Order::factory()->delivered()->create([
        'return_request_status' => 'pending',
        'return_reason' => 'Packaging damaged.',
        'return_requested_at' => now()->subHour(),
    ]);

    $response = $this->actingAs($admin, 'admin')
        ->patch(route('admin.orders.return.deny', $order));

    $response->assertRedirect();
    expect($order->fresh()->status)->toBe('delivered')
        ->and($order->fresh()->return_request_status)->toBe('denied');
});

test('admin can filter orders by pending return approval', function () {
    $admin = Admin::factory()->create([
        'is_active' => true,
    ]);

    $pendingApprovalOrder = Order::factory()->delivered()->create([
        'return_request_status' => 'pending',
        'return_reason' => 'Damaged bag seal.',
    ]);

    $regularOrder = Order::factory()->create([
        'status' => 'processing',
    ]);

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.orders.index', ['status' => 'pending_approval']));

    $response->assertStatus(200);
    $response->assertSee($pendingApprovalOrder->order_number);
    $response->assertDontSee($regularOrder->order_number);
    $response->assertSee('Pending Approval');
});
