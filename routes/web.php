<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\KpiController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Product Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');
    Route::get('/category/{category:slug}', [ProductController::class, 'category'])->name('category');
});

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/increase/{cartItem}', [CartController::class, 'increase'])->name('increase');
    Route::patch('/decrease/{cartItem}', [CartController::class, 'decrease'])->name('decrease');
    Route::post('/increase-ajax/{cartItem}', [CartController::class, 'increaseAjax'])->name('increase.ajax');
    Route::post('/decrease-ajax/{cartItem}', [CartController::class, 'decreaseAjax'])->name('decrease.ajax');
    Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
});

// Customer Authentication Routes
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/register', [CustomerAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    Route::get('/forgot-password', [CustomerAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [CustomerAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [CustomerAuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [CustomerAuthController::class, 'reset'])->name('password.update');
});

// Protected Customer Routes
Route::middleware(['auth:customer'])->group(function () {
    // Checkout Routes
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success/{orderNumber}', [CheckoutController::class, 'success'])->name('success');
        Route::post('/buy-now', [CheckoutController::class, 'buyNow'])->name('buyNow');
    });

    // Order routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::patch('/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('cancel');
        Route::patch('/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('confirm-received');
        Route::post('/track-order', [OrderController::class, 'showTrackingForm'])->name('track.form');
        Route::post('/track-package', [OrderController::class, 'trackPackage'])->name('track-package');
    });

    // Customer Account Routes
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [ProfileController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::get('/addresses', [ProfileController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [ProfileController::class, 'storeAddress'])->name('addresses.store');
        Route::patch('/addresses/{address}', [ProfileController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}', [ProfileController::class, 'destroyAddress'])->name('addresses.destroy');
        Route::get('/preferences', [ProfileController::class, 'preferences'])->name('preferences');
        Route::patch('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/addresses/get', [ProfileController::class, 'getUserAddress'])->name('addresses.show');
    });
});
// Contact Routes
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// About/Legal Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-service', [PageController::class, 'terms'])->name('terms');
Route::get('/shipping-returns', [PageController::class, 'shipping'])->name('shipping');


Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware(['auth:admin'])->group(function () {
        // Dashboard
        Route::get('/', [KpiController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [KpiController::class, 'index']);
        Route::get('/kpi/export', [App\Http\Controllers\Admin\KpiController::class, 'export'])->name('kpi.export');
        Route::get('/kpi/chart-data', [App\Http\Controllers\Admin\KpiController::class, 'getChartData'])->name('kpi.chart-data');
        Route::get('/kpi/analytics-summary', [App\Http\Controllers\Admin\KpiController::class, 'getAnalyticsSummary'])->name('kpi.analytics-summary');

    


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
        Route::resource('coupons', AdminCouponController::class);
        Route::post('/coupons/bulk-action', [AdminCouponController::class, 'bulkAction'])->name('coupons.bulk-action');

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