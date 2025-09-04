<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;

    protected $table = 'shopping_cart';

    protected $fillable = [
        'session_id',
        'customer_id',
        'product_id',
        'quantity',
        'product_options'
    ];

    protected $casts = [
      'session_id' => 'int',
      'customer_id' => 'integer',
      'quantity' => 'int',
      'product_options' => 'array'
    ];

    public function product()
    {
      return $this->belongsTo(Product::class);
    }

    public function customer()
    {
      return $this->belongsTo(Customer::class);
    }

    public function getSubtotalAttribute()
    {
      return $this->quantity * $this->product->discounted_price;
    }

  public function scopeForSession($query, $sessionId)
    {
      return $query->where('session_id', $sessionId);
    }

    public function scopeForCustomer($query, $customerId)
    {
      return $query->where('customer_id', $customerId);
    }

    public function scopeForGuest($query, $sessionId)
    {
      return $query->where('session_id', $sessionId)->whereNull('customer_id');
    }

    public function scopeForUser($query, $customerId)
    {
      return $query->where('customer_id', $customerId);
    }
}