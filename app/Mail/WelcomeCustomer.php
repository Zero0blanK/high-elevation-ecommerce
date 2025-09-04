<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeCustomer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function build()
    {
        return $this->subject('Welcome to ' . config('app.name'))
            ->view('emails.customer.welcome')
            ->with([
                'customer' => $this->customer,
                'loginUrl' => route('customer.login'),
                'shopUrl' => route('products.index')
            ]);
    }
}