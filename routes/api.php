<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerAddressController as ApiCustomerAddressController;
use App\Http\Controllers\Api\WebhookController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

// Product API
Route::prefix('products')->group(function () {
    Route::get('/', [ApiProductController::class, 'index']);
    Route::get('/featured', [ApiProductController::class, 'featured']);
    Route::get('/search', [ApiProductController::class, 'search']);
    Route::get('/{product}', [ApiProductController::class, 'show']);
});

// Categories API
Route::get('/categories', [ApiProductController::class, 'categories']);

// Cart API (Session-based for guests)
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/add', [CartController::class, 'store']);
    Route::patch('/{cartItem}', [CartController::class, 'update']);
    Route::delete('/{cartItem}', [CartController::class, 'destroy']);
    Route::delete('/', [CartController::class, 'clear']);
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon']);
});

// Authentication API
// Webhooks
Route::prefix('webhooks')->group(function () {
    Route::post('/stripe', [WebhookController::class, 'stripeWebhook']);
});

/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::patch('/profile', [AuthController::class, 'updateProfile']);
        Route::patch('/change-password', [AuthController::class, 'changePassword']);
    });

    // Customer Addresses
    Route::apiResource('addresses', ApiCustomerAddressController::class);

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [ApiOrderController::class, 'index']);
        Route::post('/', [ApiOrderController::class, 'store']);
        Route::get('/{order}', [ApiOrderController::class, 'show']);
        Route::post('/{order}/confirm-payment', [ApiOrderController::class, 'confirmPayment']);
        Route::patch('/{order}/cancel', [ApiOrderController::class, 'cancel']);
        Route::get('/{order}/track', [ApiOrderController::class, 'track']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    // Dashboard API
    Route::get('/dashboard-metrics', [AdminDashboardController::class, 'getDashboardMetrics']);
    Route::get('/chart-data/{type}', [AdminDashboardController::class, 'getChartData']);

    // Products API
    Route::apiResource('products', AdminProductController::class);
    Route::post('/products/bulk-action', [AdminProductController::class, 'bulkAction']);

    // Orders API
    Route::apiResource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);

    // Customers API
    Route::apiResource('customers', AdminCustomerController::class)->only(['index', 'show', 'update', 'destroy']);

    // Analytics API
    Route::prefix('analytics')->group(function () {
        Route::get('/sales', [AdminAnalyticsController::class, 'salesReport']);
        Route::get('/customers', [AdminAnalyticsController::class, 'customerReport']);
        Route::get('/inventory', [AdminAnalyticsController::class, 'inventoryReport']);
    });
});