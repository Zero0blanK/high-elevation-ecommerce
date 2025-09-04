<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AbandonedCart extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $customer;
    public $cartItems;

    public function __construct(Customer $customer, Collection $cartItems)
    {
        $this->customer = $customer;
        $this->cartItems = $cartItems;
    }

    public function build()
    {
        $subtotal = $this->cartItems->sum('total_price');
        
        return $this->subject('Don\'t forget your coffee!')
            ->view('emails.customer.abandoned_cart')
            ->with([
                'customer' => $this->customer,
                'cartItems' => $this->cartItems,
                'subtotal' => $subtotal,
                'checkoutUrl' => route('checkout.index'),
                'couponCode' => 'COMEBACK10' // 10% discount for abandoned cart
            ]);
    }
}