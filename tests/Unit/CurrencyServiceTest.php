<?php

use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can format price with default currency', function () {
    Currency::create([
        'code' => 'USD',
        'name' => 'US Dollar',
        'symbol' => '$',
        'exchange_rate' => 1.000000,
        'position' => 'before',
        'is_active' => true,
        'is_default' => true,
    ]);

    $service = app(CurrencyService::class);
    $formatted = $service->format(25.99);

    expect($formatted)->toBe('$25.99');
});

test('can convert between currencies', function () {
    Currency::create([
        'code' => 'USD',
        'name' => 'US Dollar',
        'symbol' => '$',
        'exchange_rate' => 1.000000,
        'position' => 'before',
        'is_active' => true,
        'is_default' => true,
    ]);

    Currency::create([
        'code' => 'EUR',
        'name' => 'Euro',
        'symbol' => '€',
        'exchange_rate' => 0.920000,
        'position' => 'before',
        'is_active' => true,
        'is_default' => false,
    ]);

    $service = app(CurrencyService::class);
    $converted = $service->convert(100, 'EUR');

    expect($converted)->toBe(92.0);
});

test('active currencies are returned', function () {
    Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1, 'is_active' => true, 'is_default' => true]);
    Currency::create(['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.92, 'is_active' => true, 'is_default' => false]);
    Currency::create(['code' => 'XYZ', 'name' => 'Inactive', 'symbol' => 'X', 'exchange_rate' => 1, 'is_active' => false, 'is_default' => false]);

    $service = app(CurrencyService::class);
    $currencies = $service->getActiveCurrencies();

    expect($currencies)->toHaveCount(2);
});
