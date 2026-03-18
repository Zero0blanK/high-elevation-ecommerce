<?php

namespace App\Http\Middleware;

use App\Services\CurrencyService;
use Closure;
use Illuminate\Http\Request;

class CurrencyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $currencyCode = $request->query('currency')
            ?? $request->cookie('currency')
            ?? session('currency')
            ?? config('ecommerce.currency.code', 'USD');

        app()->singleton('current_currency', function () use ($currencyCode) {
            $currencyService = app(CurrencyService::class);
            return $currencyService->getCurrency($currencyCode)
                ?? $currencyService->getDefaultCurrency();
        });

        session(['currency' => $currencyCode]);

        $response = $next($request);

        if (method_exists($response, 'cookie')) {
            $response->cookie('currency', $currencyCode, 43200); // 30 days
        }

        return $response;
    }
}
