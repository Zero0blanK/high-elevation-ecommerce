<?php

namespace App\Services;

use App\Repositories\CurrencyRepository;
use App\Models\Currency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    public function __construct(
        protected CurrencyRepository $currencyRepository
    ) {}

    public function getActiveCurrencies(): Collection
    {
        return Cache::remember('currencies.active', 3600, function () {
            return $this->currencyRepository->getActive();
        });
    }

    public function getDefaultCurrency(): Currency
    {
        return Cache::remember('currencies.default', 3600, function () {
            return $this->currencyRepository->getDefault()
                ?? $this->currencyRepository->findByCode('USD');
        });
    }

    public function getCurrency(string $code): ?Currency
    {
        return $this->currencyRepository->findByCode($code);
    }

    public function convert(float $amount, string $to, ?string $from = null): float
    {
        $toCurrency = $this->getCurrency($to);
        if (!$toCurrency) {
            return $amount;
        }

        $fromCurrency = $from ? $this->getCurrency($from) : null;
        return $toCurrency->convert($amount, $fromCurrency);
    }

    public function format(float $amount, ?string $currencyCode = null): string
    {
        $currency = $currencyCode
            ? $this->getCurrency($currencyCode)
            : $this->getDefaultCurrency();

        if (!$currency) {
            return '$' . number_format($amount, 2);
        }

        return $currency->format($amount);
    }

    public function updateExchangeRates(): void
    {
        try {
            $baseCurrency = $this->getDefaultCurrency();
            $apiKey = config('services.exchange_rate.api_key', '');
            $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$baseCurrency->code}";

            $response = Http::get($url);

            if ($response->successful()) {
                $rates = $response->json('conversion_rates', []);
                $activeCurrencies = $this->currencyRepository->getActive();

                foreach ($activeCurrencies as $currency) {
                    if (isset($rates[$currency->code])) {
                        $this->currencyRepository->updateExchangeRate(
                            $currency->code,
                            $rates[$currency->code]
                        );
                    }
                }

                Cache::forget('currencies.active');
                Cache::forget('currencies.default');
            }
        } catch (\Exception $e) {
            Log::error('Failed to update exchange rates', ['error' => $e->getMessage()]);
        }
    }
}
