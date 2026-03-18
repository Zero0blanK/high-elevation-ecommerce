<?php

namespace App\Observers;

use App\Models\Payment;
use App\Events\PaymentProcessed;
use App\Events\PaymentFailed;

class PaymentObserver
{
    public function updated(Payment $payment): void
    {
        if (!$payment->isDirty('status')) {
            return;
        }

        $order = $payment->order;

        match ($payment->status) {
            'completed' => event(new PaymentProcessed($payment, $order)),
            'failed' => event(new PaymentFailed($payment, $order, 'Payment processing failed')),
            default => null,
        };
    }
}
