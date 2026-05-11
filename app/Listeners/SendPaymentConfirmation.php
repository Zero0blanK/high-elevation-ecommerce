<?php

namespace App\Listeners;

use App\Events\PaymentProcessed;
use App\Notifications\PaymentReceived;

class SendPaymentConfirmation
{
    public function handle(PaymentProcessed $event): void
    {
        $event->order->customer->notifyNow(new PaymentReceived($event->payment, $event->order));
    }
}
