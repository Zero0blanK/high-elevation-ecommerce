<?php

namespace App\Jobs;

use App\Services\Payment\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        protected Order $order,
        protected string $gateway,
        protected array $paymentData = []
    ) {}

    public function handle(PaymentGatewayInterface $gatewayService): void
    {
        $gatewayService->createPayment($this->order, $this->paymentData);
    }
}
