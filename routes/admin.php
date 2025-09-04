<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\AdminUserController;

Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware(['auth:admin'])->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        Route::get('/chart-data/{type}', [AdminDashboardController::class, 'getChartData'])->name('chart-data');

        // Admin Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [AdminProfileController::class, 'show'])->name('show');
            Route::get('/edit', [AdminProfileController::class, 'edit'])->name('edit');
            Route::patch('/', [AdminProfileController::class, 'update'])->name('update');
            Route::patch('/password', [AdminProfileController::class, 'updatePassword'])->name('password.update');
        });

        // Product Management
        Route::resource('products', AdminProductController::class);
        Route::post('/products/bulk-action', [AdminProductController::class, 'bulkAction'])->name('products.bulk-action');
        Route::patch('/products/{product}/stock', [AdminProductController::class, 'updateStock'])->name('products.update-stock');
        Route::delete('/products/{product}/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.delete-image');

        // Category Management
        Route::resource('categories', AdminCategoryController::class);
        Route::post('/categories/bulk-action', [AdminCategoryController::class, 'bulkAction'])->name('categories.bulk-action');

        // Order Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::patch('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
            Route::post('/{order}/refund', [AdminOrderController::class, 'refund'])->name('refund');
            Route::get('/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('invoice');
            Route::get('/{order}/shipping-label', [AdminOrderController::class, 'printShippingLabel'])->name('shipping-label');
        });

        // Customer Management
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [AdminCustomerController::class, 'index'])->name('index');
            Route::get('/{customer}', [AdminCustomerController::class, 'show'])->name('show');
            Route::get('/{customer}/edit', [AdminCustomerController::class, 'edit'])->name('edit');
            Route::patch('/{customer}', [AdminCustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [AdminCustomerController::class, 'destroy'])->name('destroy');
            Route::post('/send-email', [AdminCustomerController::class, 'sendEmail'])->name('send-email');
        });

        // Inventory Management
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [AdminInventoryController::class, 'index'])->name('index');
            Route::get('/logs', [AdminInventoryController::class, 'logs'])->name('logs');
            Route::post('/bulk-update', [AdminInventoryController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/low-stock', [AdminInventoryController::class, 'lowStock'])->name('low-stock');
        });

        // Analytics & Reporting
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [AdminAnalyticsController::class, 'index'])->name('index');
            Route::get('/sales', [AdminAnalyticsController::class, 'salesReport'])->name('sales');
            Route::get('/customers', [AdminAnalyticsController::class, 'customerReport'])->name('customers');
            Route::get('/inventory', [AdminAnalyticsController::class, 'inventoryReport'])->name('inventory');
            Route::post('/export', [AdminAnalyticsController::class, 'exportReport'])->name('export');
        });

        // Coupon Management
        // Route::resource('coupons', AdminCouponController::class);
        // Route::post('/coupons/bulk-action', [AdminCouponController::class, 'bulkAction'])->name('coupons.bulk-action');

        // Settings Management (Super Admin & Admin only)
        Route::middleware(['check.admin.role:super_admin,admin'])->prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
            Route::patch('/general', [AdminSettingsController::class, 'updateGeneral'])->name('general');
            Route::patch('/email', [AdminSettingsController::class, 'updateEmail'])->name('email');
            Route::patch('/payment', [AdminSettingsController::class, 'updatePayment'])->name('payment');
            Route::patch('/shipping', [AdminSettingsController::class, 'updateShipping'])->name('shipping');
        });

        // Admin User Management (Super Admin only)
        Route::middleware(['check.admin.role:super_admin'])->prefix('admin-users')->name('admin-users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/create', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{adminUser}', [AdminUserController::class, 'show'])->name('show');
            Route::get('/{adminUser}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::patch('/{adminUser}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{adminUser}', [AdminUserController::class, 'destroy'])->name('destroy');
        });
    });
});