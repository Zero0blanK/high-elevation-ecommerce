<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'currency',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'shipping_method',
        'tracking_number',
        'notes',
        'shipped_at',
        'delivered_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function orderAddresses()
    {
        return $this->belongsTo(OrderAddress::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    public function customerAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'id');
    }

    public function shippingAddress()
    {
        return $this->hasOneThrough(
            CustomerAddress::class,
            OrderAddress::class,
            'order_id', // Foreign key on order_addresses table
            'id', // Foreign key on customer_addresses table
            'id', // Local key on orders table
            'address_id' // Local key on order_addresses table
        );
    }

    public function billingAddress()
    {
        return $this->hasOneThrough(
            CustomerAddress::class,
            OrderAddress::class,
            'order_id', // Foreign key on order_addresses table
            'id', // Foreign key on customer_addresses table
            'id', // Local key on orders table
            'address_id' // Local key on order_addresses table
        );
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBeShipped()
    {
        return $this->status === 'processing' && $this->isPaid();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . strtoupper(uniqid());
        });
    }
}
