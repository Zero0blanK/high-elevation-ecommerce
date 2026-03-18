<?php

namespace App\Repositories;

use App\Models\Currency;
use Illuminate\Support\Collection;

class CurrencyRepository
{
    protected Currency $model;

    public function __construct(Currency $currency)
    {
        $this->model = $currency;
    }

    public function getAll(): Collection
    {
        return $this->model->newQuery()->orderBy('name')->get();
    }

    public function getActive(): Collection
    {
        return $this->model->newQuery()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getDefault(): ?Currency
    {
        return $this->model->newQuery()
            ->where('is_default', true)
            ->first();
    }

    public function findByCode(string $code): ?Currency
    {
        return $this->model->newQuery()
            ->where('code', $code)
            ->first();
    }

    public function findById(int $id): ?Currency
    {
        return $this->model->newQuery()->find($id);
    }

    public function create(array $data): Currency
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Currency $currency, array $data): bool
    {
        return $currency->update($data);
    }

    public function updateExchangeRate(string $code, float $rate): bool
    {
        return $this->model->newQuery()
            ->where('code', $code)
            ->update([
                'exchange_rate' => $rate,
                'rate_updated_at' => now(),
            ]) > 0;
    }

    public function bulkUpdateRates(array $rates): void
    {
        foreach ($rates as $code => $rate) {
            $this->updateExchangeRate($code, $rate);
        }
    }
}
