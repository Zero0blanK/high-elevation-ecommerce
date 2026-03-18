<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Observers\PaymentObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        Product::observe(ProductObserver::class);
        Payment::observe(PaymentObserver::class);

        View::composer('admin.partials.sidebar', function ($view) {
            $view->with([
                'pendingOrdersCount' => Order::where('status', 'pending')->count(),
                'lowStockCount' => Product::where('is_active', true)
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->count(),
            ]);
        });
    }
}
