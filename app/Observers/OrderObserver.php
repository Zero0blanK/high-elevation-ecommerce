<?php

namespace App\Observers;

use App\Models\Order;
use App\Events\OrderStatusChanged;

class OrderObserver
{
    public function updating(Order $order): void
    {
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            event(new OrderStatusChanged($order, $oldStatus, $newStatus));
        }
    }
}
