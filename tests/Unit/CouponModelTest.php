<?php

use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('valid coupon returns true', function () {
    $coupon = Coupon::factory()->create([
        'is_active' => true,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addMonth(),
    ]);

    expect($coupon->isValid())->toBeTrue();
});

test('expired coupon returns false', function () {
    $coupon = Coupon::factory()->expired()->create();

    expect($coupon->isValid())->toBeFalse();
});

test('inactive coupon returns false', function () {
    $coupon = Coupon::factory()->create(['is_active' => false]);

    expect($coupon->isValid())->toBeFalse();
});

test('percentage discount is calculated correctly', function () {
    $coupon = Coupon::factory()->create([
        'type' => 'percentage',
        'value' => 10,
        'minimum_amount' => 0,
        'is_active' => true,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addMonth(),
    ]);

    $discount = $coupon->calculateDiscount(100);

    expect($discount)->toBe(10.0);
});

test('fixed discount is calculated correctly', function () {
    $coupon = Coupon::factory()->create([
        'type' => 'fixed',
        'value' => 15,
        'minimum_amount' => 0,
        'is_active' => true,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addMonth(),
    ]);

    $discount = $coupon->calculateDiscount(100);

    expect($discount)->toBe(15.0);
});

test('discount respects minimum amount', function () {
    $coupon = Coupon::factory()->create([
        'type' => 'percentage',
        'value' => 10,
        'minimum_amount' => 50,
        'is_active' => true,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addMonth(),
    ]);

    expect($coupon->calculateDiscount(30))->toBe(0.0);
    expect($coupon->calculateDiscount(100))->toBe(10.0);
});

test('usage limit is respected', function () {
    $coupon = Coupon::factory()->create([
        'usage_limit' => 5,
        'used_count' => 5,
        'is_active' => true,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addMonth(),
    ]);

    expect($coupon->isValid())->toBeFalse();
});
