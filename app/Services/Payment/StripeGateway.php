<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function getGatewayName(): string
    {
        return 'stripe';
    }

    public function createPayment(Order $order, array $data = []): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($order->total_amount * 100),
                'currency' => strtolower($order->currency),
                'payment_method_types' => ['card'],
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                'description' => "Payment for order #{$order->order_number}",
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'card',
                'payment_gateway' => $this->getGatewayName(),
                'transaction_id' => $paymentIntent->id,
                'amount' => $order->total_amount,
                'currency' => $order->currency,
                'status' => 'pending',
                'gateway_response' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                ],
            ]);

            return [
                'payment' => $payment,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe: Payment creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function confirmPayment(string $transactionId, Order $order): bool
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($transactionId);
            $payment = Payment::where('transaction_id', $transactionId)
                ->where('order_id', $order->id)
                ->firstOrFail();

            if ($paymentIntent->status === 'succeeded') {
                $payment->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $paymentIntent->charges->data[0]->id ?? null,
                    'processed_at' => now(),
                    'gateway_response' => array_merge($payment->gateway_response ?? [], [
                        'payment_intent' => $paymentIntent->toArray(),
                    ]),
                ]);

                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Stripe: Payment confirmation failed', [
                'transaction_id' => $transactionId,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function refund(Payment $payment, ?float $amount = null): mixed
    {
        try {
            $refundAmount = $amount ? (int) ($amount * 100) : (int) ($payment->amount * 100);

            $refund = Refund::create([
                'payment_intent' => $payment->transaction_id,
                'amount' => $refundAmount,
                'metadata' => [
                    'order_id' => $payment->order_id,
                    'reason' => 'requested_by_customer',
                ],
            ]);

            $payment->update([
                'status' => ($amount && $amount < $payment->amount) ? 'partially_refunded' : 'refunded',
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'refund' => $refund->toArray(),
                ]),
            ]);

            $payment->order->update([
                'payment_status' => 'refunded',
                'status' => 'refunded',
            ]);

            return $refund;
        } catch (\Exception $e) {
            Log::error('Stripe: Refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function handleWebhook(string $payload, string $signature): bool
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (\Exception $e) {
            Log::error('Stripe: Webhook verification failed', ['error' => $e->getMessage()]);
            return false;
        }

        match ($event['type']) {
            'payment_intent.succeeded' => $this->handleSucceeded($event['data']['object']),
            'payment_intent.payment_failed' => $this->handleFailed($event['data']['object']),
            default => Log::info('Stripe: Unhandled webhook', ['type' => $event['type']]),
        };

        return true;
    }

    private function handleSucceeded(array $paymentIntent): void
    {
        $payment = Payment::where('transaction_id', $paymentIntent['id'])->first();
        if ($payment && $payment->status === 'pending') {
            $this->confirmPayment($paymentIntent['id'], $payment->order);
        }
    }

    private function handleFailed(array $paymentIntent): void
    {
        $payment = Payment::where('transaction_id', $paymentIntent['id'])->first();
        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'failure_reason' => $paymentIntent['last_payment_error']['message'] ?? 'Unknown',
                ]),
            ]);
            $payment->order->update(['payment_status' => 'failed']);
        }
    }
}
