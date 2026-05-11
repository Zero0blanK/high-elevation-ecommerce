<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use Illuminate\Support\Facades\Log;

class HandlePaymentFailure
{
    public function handle(PaymentFailed $event): void
    {
        Log::warning('Payment failed', [
            'order_id' => $event->order->id,
            'order_number' => $event->order->order_number,
            'payment_id' => $event->payment->id,
            'reason' => $event->reason,
        ]);

        app(\App\Services\CheckoutService::class)->failOrderPayment($event->order, $event->payment);
    }
}
