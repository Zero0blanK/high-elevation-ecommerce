<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'position',
        'decimal_places',
        'is_active',
        'is_default',
        'rate_updated_at',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'rate_updated_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function format($amount): string
    {
        $formatted = number_format($amount * $this->exchange_rate, $this->decimal_places);

        return $this->position === 'before'
            ? $this->symbol . $formatted
            : $formatted . $this->symbol;
    }

    public function convert($amount, Currency $from = null): float
    {
        if ($from) {
            $amountInBase = $amount / $from->exchange_rate;
            return round($amountInBase * $this->exchange_rate, $this->decimal_places);
        }

        return round($amount * $this->exchange_rate, $this->decimal_places);
    }
}
