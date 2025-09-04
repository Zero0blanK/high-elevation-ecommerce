<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderDelivered extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Order Delivered #' . $this->order->order_number)
            ->view('emails.order.delivered')
            ->with([
                'order' => $this->order,
                'customer' => $this->order->customer,
                'reviewUrl' => route('products.review', ['order' => $this->order->id]),
                'reorderUrl' => route('orders.reorder', $this->order->id)
            ]);
    }
}