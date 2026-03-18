<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function createPayment(Order $order, array $data = []): array;

    public function confirmPayment(string $transactionId, Order $order): bool;

    public function refund(Payment $payment, ?float $amount = null): mixed;

    public function handleWebhook(string $payload, string $signature): bool;

    public function getGatewayName(): string;
}
