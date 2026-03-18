<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Notifications\OrderShipped;
use App\Notifications\OrderDelivered;

class SendOrderStatusNotification
{
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order->load('customer');
        $customer = $order->customer;

        if (!$customer) {
            return;
        }

        match ($event->newStatus) {
            'shipped' => $customer->notify(new OrderShipped($order)),
            'delivered' => $customer->notify(new OrderDelivered($order)),
            default => null,
        };
    }
}
