<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Order Shipped #' . $this->order->order_number)
            ->view('emails.order.shipped')
            ->with([
                'order' => $this->order,
                'customer' => $this->order->customer,
                'trackingUrl' => $this->getTrackingUrl(),
                'orderTrackingUrl' => route('orders.track', $this->order->order_number)
            ]);
    }

    private function getTrackingUrl()
    {
        if (!$this->order->tracking_number) {
            return null;
        }

        // Generate tracking URL based on shipping method
        switch (strtolower($this->order->shipping_method)) {
            case 'fedex':
                return 'https://www.fedex.com/apps/fedextrack/?tracknumbers=' . $this->order->tracking_number;
            case 'ups':
                return 'https://www.ups.com/track?tracknum=' . $this->order->tracking_number;
            case 'usps':
                return 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . $this->order->tracking_number;
            default:
                return null;
        }
    }
}