<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BirthdayDiscount extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $customer;
    public $coupon;

    public function __construct(Customer $customer, Coupon $coupon)
    {
        $this->customer = $customer;
        $this->coupon = $coupon;
    }

    public function build()
    {
        return $this->subject('Happy Birthday! Here\'s a special gift for you 🎂')
            ->view('emails.customer.birthday_discount')
            ->with([
                'customer' => $this->customer,
                'coupon' => $this->coupon,
                'shopUrl' => route('products.index'),
                'discountPercentage' => $this->coupon->value
            ]);
    }
}