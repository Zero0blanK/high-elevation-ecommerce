<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Order Confirmation #' . $this->order->order_number)
            ->view('emails.order.confirmation')
            ->with([
                'order' => $this->order,
                'customer' => $this->order->customer,
                'trackingUrl' => route('orders.track', $this->order->order_number)
            ]);
    }
}