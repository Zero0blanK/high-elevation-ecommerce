<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReorderReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $customer;
    public $lastOrder;

    public function __construct(Customer $customer, Order $lastOrder)
    {
        $this->customer = $customer;
        $this->lastOrder = $lastOrder;
    }

    public function build()
    {
        return $this->subject('Time to restock your favorite coffee?')
            ->view('emails.customer.reorder_reminder')
            ->with([
                'customer' => $this->customer,
                'lastOrder' => $this->lastOrder,
                'reorderUrl' => route('orders.reorder', $this->lastOrder->id),
                'shopUrl' => route('products.index')
            ]);
    }
}