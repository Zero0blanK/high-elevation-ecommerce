<?php

namespace App\Events;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentProcessed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Payment $payment,
        public Order $order
    ) {}
}
