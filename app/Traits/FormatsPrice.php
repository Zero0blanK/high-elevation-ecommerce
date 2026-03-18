<?php

namespace App\Traits;

use App\Models\Currency;

trait FormatsPrice
{
    public function formatPrice($amount, ?string $currencyCode = null): string
    {
        if ($currencyCode) {
            $currency = Currency::where('code', $currencyCode)->first();
            if ($currency) {
                return $currency->format($amount);
            }
        }

        $symbol = config('ecommerce.currency.symbol', '$');
        $position = config('ecommerce.currency.position', 'before');
        $formatted = number_format($amount, 2);

        return $position === 'before'
            ? $symbol . $formatted
            : $formatted . $symbol;
    }

    public function convertPrice($amount, string $toCurrency, ?string $fromCurrency = null): float
    {
        $to = Currency::where('code', $toCurrency)->first();
        if (!$to) {
            return $amount;
        }

        $from = $fromCurrency
            ? Currency::where('code', $fromCurrency)->first()
            : null;

        return $to->convert($amount, $from);
    }
}
