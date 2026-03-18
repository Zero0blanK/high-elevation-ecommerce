<?php

namespace App\Listeners;

use App\Events\PaymentProcessed;
use App\Notifications\PaymentReceived;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPaymentConfirmation implements ShouldQueue
{
    public function handle(PaymentProcessed $event): void
    {
        $event->order->customer->notify(new PaymentReceived($event->payment, $event->order));
    }
}
