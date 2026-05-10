<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function createPaymentIntent(Order $order, array $paymentData = []): array
    {
        $paymongoSecretKey = config('services.paymongo.secret_key');

        if (empty($paymongoSecretKey)) {
            throw new \Exception('PayMongo is not configured.');
        }

        $customerName = trim(($order->customer->first_name ?? '') . ' ' . ($order->customer->last_name ?? ''));

        $response = Http::withBasicAuth($paymongoSecretKey, '')
            ->post('https://api.paymongo.com/v1/sources', [
                'data' => [
                    'attributes' => [
                        'amount' => (int) ($order->total_amount * 100),
                        'redirect' => [
                            'success' => route('checkout.paymongo.success', ['orderNumber' => $order->order_number]),
                            'failed' => route('checkout.paymongo.failed', ['orderNumber' => $order->order_number]),
                        ],
                        'type' => 'gcash',
                        'currency' => 'PHP',
                        'billing' => [
                            'name' => $customerName !== '' ? $customerName : 'Customer',
                            'email' => $order->customer->email ?? '',
                            'phone' => $order->customer->phone ?? null,
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            Log::error('PayMongo source creation failed', [
                'order_id' => $order->id,
                'response' => $response->json(),
            ]);

            throw new \Exception('Failed to create PayMongo payment.');
        }

        $source = $response->json('data');

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'paymongo',
            'payment_gateway' => 'paymongo',
            'transaction_id' => $source['id'] ?? null,
            'amount' => $order->total_amount,
            'currency' => $order->currency,
            'status' => 'pending',
            'gateway_response' => $source ?? [],
        ]);

        return [
            'payment' => $payment,
            'payment_url' => $source['attributes']['redirect']['checkout_url'] ?? null,
            'transaction_id' => $source['id'] ?? null,
        ];
    }

    public function confirmPayment($transactionId, Order $order): bool
    {
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('order_id', $order->id)
            ->first();

        if (!$payment) {
            throw new \Exception('Payment record not found');
        }

        $payment->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        return true;
    }

    public function refundPayment(Payment $payment, $amount = null)
    {
        throw new \Exception('Refund is not yet supported for PayMongo in this integration.');
    }

    public function handleWebhook($payload, $signature)
    {
        $event = json_decode($payload, true);

        if (!is_array($event)) {
            throw new \Exception('Invalid webhook payload');
        }

        $eventType = data_get($event, 'data.attributes.type') ?? data_get($event, 'type');

        $candidateTransactionIds = array_filter([
            data_get($event, 'data.attributes.data.id'),
            data_get($event, 'data.attributes.data.attributes.id'),
            data_get($event, 'data.attributes.data.attributes.source.id'),
            data_get($event, 'data.attributes.id'),
        ]);

        if (empty($candidateTransactionIds)) {
            return true;
        }

        $payment = Payment::whereIn('transaction_id', $candidateTransactionIds)->first();

        if (!$payment) {
            return true;
        }

        if (in_array($eventType, ['payment.paid', 'source.chargeable'], true)) {
            $payment->update([
                'status' => 'completed',
                'processed_at' => now(),
                'gateway_response' => array_merge($payment->gateway_response ?? [], ['webhook' => $event]),
            ]);

            $payment->order->update([
                'payment_status' => 'paid',
                'status' => $payment->order->status === 'pending' ? 'processing' : $payment->order->status,
            ]);
        }

        if (in_array($eventType, ['payment.failed', 'source.failed'], true)) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => array_merge($payment->gateway_response ?? [], ['webhook' => $event]),
            ]);

            $payment->order->update(['payment_status' => 'failed']);
        }

        return true;
    }
}
