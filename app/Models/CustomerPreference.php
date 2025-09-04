<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'preferred_roast_level',
        'preferred_grind_type',
        'favorite_origins',
        'marketing_emails',
        'order_notifications'
    ];

    protected $casts = [
        'marketing_emails' => 'boolean',
        'order_notifications' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}