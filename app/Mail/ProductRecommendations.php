<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ProductRecommendations extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $customer;
    public $recommendations;

    public function __construct(Customer $customer, Collection $recommendations)
    {
        $this->customer = $customer;
        $this->recommendations = $recommendations;
    }

    public function build()
    {
        return $this->subject('New coffee recommendations just for you!')
            ->view('emails.customer.product_recommendations')
            ->with([
                'customer' => $this->customer,
                'recommendations' => $this->recommendations,
                'shopUrl' => route('products.index')
            ]);
    }
}