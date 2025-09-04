<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Payment Failed for Order #' . $this->order->order_number)
            ->view('emails.payment.failed')
            ->with([
                'order' => $this->order,
                'customer' => $this->order->customer,
                'retryUrl' => route('orders.payment.retry', $this->order->id),
                'supportUrl' => route('support.contact')
            ]);
    }
}