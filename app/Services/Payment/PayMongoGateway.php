<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoGateway implements PaymentGatewayInterface
{
    protected string $secretKey;
    protected string $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key', '');
    }

    public function getGatewayName(): string
    {
        return 'paymongo';
    }

    public function createPayment(Order $order, array $data = []): array
    {
        $paymentMethod = $data['payment_method'] ?? 'gcash';

        if ($paymentMethod === 'paymongo_card') {
            $result = $this->createCheckoutSession($order);
        } else {
            $result = $this->createSource($order, $paymentMethod);
        }

        return array_merge($result, [
            'requires_action' => !empty($result['payment_url']),
        ]);
    }

    protected function createSource(Order $order, string $type = 'gcash'): array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/sources", [
                    'data' => [
                        'attributes' => [
                            'amount' => (int) ($order->total_amount * 100),
                            'redirect' => [
                                'success' => route('checkout.paymongo.success', ['orderNumber' => $order->order_number]),
                                'failed' => route('checkout.paymongo.failed', ['orderNumber' => $order->order_number]),
                            ],
                            'type' => 'gcash', // PayMongo sources currently mostly support gcash/grab_pay
                            'currency' => 'PHP',
                            'billing' => [
                                'name' => trim($order->customer->first_name . ' ' . $order->customer->last_name),
                                'email' => $order->customer->email,
                                'phone' => $order->customer->phone ?? null,
                            ],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('PayMongo source creation failed', ['response' => $response->json()]);
                throw new \Exception('Failed to create PayMongo payment source.');
            }

            $source = $response->json('data');

            return [
                'success' => true,
                'payment_url' => $source['attributes']['redirect']['checkout_url'],
                'transaction_id' => $source['id'],
                'gateway_response' => $source,
            ];
        } catch (\Exception $e) {
            Log::error('PayMongo source error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function createCheckoutSession(Order $order): array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/checkout_sessions", [
                    'data' => [
                        'attributes' => [
                            'billing' => [
                                'name' => trim($order->customer->first_name . ' ' . $order->customer->last_name),
                                'email' => $order->customer->email,
                                'phone' => $order->customer->phone ?? null,
                            ],
                            'line_items' => [
                                [
                                    'currency' => 'PHP',
                                    'amount' => (int) ($order->total_amount * 100),
                                    'name' => 'Order ' . $order->order_number,
                                    'quantity' => 1,
                                ],
                            ],
                            'payment_method_types' => ['card'],
                            'success_url' => route('checkout.paymongo.success', ['orderNumber' => $order->order_number]),
                            'cancel_url' => route('checkout.paymongo.failed', ['orderNumber' => $order->order_number]),
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('PayMongo checkout session failed', ['response' => $response->json()]);
                throw new \Exception('Failed to create PayMongo checkout session.');
            }

            $session = $response->json('data');

            return [
                'success' => true,
                'payment_url' => $session['attributes']['checkout_url'],
                'transaction_id' => $session['id'],
                'gateway_response' => $session,
            ];
        } catch (\Exception $e) {
            Log::error('PayMongo session error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function confirmPayment(string $transactionId, Order $order): bool
    {
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('order_id', $order->id)
            ->first();

        if (!$payment) {
            return false;
        }

        if ($payment->status === 'completed') {
            return true;
        }

        if ($payment->status === 'failed') {
            return false;
        }

        // Source flow (GCash): authorize -> source becomes chargeable -> charge source -> paid/failed.
        if (str_starts_with($transactionId, 'src_')) {
            $source = $this->getSource($transactionId);
            $sourceStatus = data_get($source, 'attributes.status');

            if (in_array($sourceStatus, ['failed', 'cancelled', 'expired'], true)) {
                $this->markPaymentFailed($payment, $source);
                return false;
            }

            if (in_array($sourceStatus, ['chargeable', 'paid'], true)) {
                $chargedPayment = $this->createPaymentFromSource(
                    $transactionId,
                    (int) ($order->total_amount * 100),
                    $order->currency
                );

                if (!$payment->gateway_transaction_id && data_get($chargedPayment, 'id')) {
                    $payment->update([
                        'gateway_transaction_id' => data_get($chargedPayment, 'id'),
                        'gateway_response' => array_merge($payment->gateway_response ?? [], ['payment' => $chargedPayment]),
                    ]);
                }

                $paymentStatus = data_get($chargedPayment, 'attributes.status');

                if ($paymentStatus === 'paid') {
                    $this->markPaymentCompleted($payment, $chargedPayment);
                    return true;
                }

                if (in_array($paymentStatus, ['failed', 'cancelled'], true)) {
                    $this->markPaymentFailed($payment, $chargedPayment);
                    return false;
                }
            }
        }

        // Checkout session flow (cards): confirm by checking session/payment status.
        if (str_starts_with($transactionId, 'cs_')) {
            $session = $this->getCheckoutSession($transactionId);
            $sessionStatuses = array_values(array_filter(array_map(
                static fn ($status) => is_string($status) ? strtolower($status) : null,
                [
                    data_get($session, 'attributes.status'),
                    data_get($session, 'attributes.payment_intent.attributes.status'),
                    data_get($session, 'attributes.payments.0.attributes.status'),
                    data_get($session, 'attributes.payments.0.status'),
                ]
            )));

            if (in_array('paid', $sessionStatuses, true)) {
                $this->markPaymentCompleted($payment, $session);
                return true;
            }

            if (array_intersect($sessionStatuses, ['failed', 'cancelled', 'expired'])) {
                $this->markPaymentFailed($payment, $session);
                return false;
            }
        }

        // For checkout sessions/cards, do not force a paid state from redirect alone.
        return false;
    }

    public function refund(Payment $payment, ?float $amount = null): mixed
    {
        // PayMongo refund implementation (optional for now as per user request to check integration)
        throw new \Exception('Refund not yet implemented for PayMongo Gateway.');
    }

    public function handleWebhook(string $payload, string $signature): bool
    {
        $event = json_decode($payload, true);
        $type = data_get($event, 'data.attributes.type');

        if ($type === 'source.chargeable') {
            $sourceId = data_get($event, 'data.attributes.data.id');
            if ($sourceId) {
                $payment = Payment::where('transaction_id', $sourceId)->first();
                if ($payment && $payment->status === 'pending') {
                    $this->confirmPayment($sourceId, $payment->order);
                }
            }
        }

        if ($type === 'payment.paid') {
            $sourceId = data_get($event, 'data.attributes.data.attributes.source.id');
            $paymongoPaymentId = data_get($event, 'data.attributes.data.id');
            if (!$sourceId && !$paymongoPaymentId) {
                return true;
            }

            $payment = Payment::where(function ($query) use ($sourceId, $paymongoPaymentId) {
                if ($sourceId) {
                    $query->where('transaction_id', $sourceId);
                }
                if ($paymongoPaymentId) {
                    $query->orWhere('gateway_transaction_id', $paymongoPaymentId);
                }
            })->first();

            if ($payment) {
                $this->markPaymentCompleted($payment, data_get($event, 'data.attributes.data'));
            }
        }

        if ($type === 'payment.failed') {
            $sourceId = data_get($event, 'data.attributes.data.attributes.source.id');
            $paymongoPaymentId = data_get($event, 'data.attributes.data.id');
            if (!$sourceId && !$paymongoPaymentId) {
                return true;
            }

            $payment = Payment::where(function ($query) use ($sourceId, $paymongoPaymentId) {
                if ($sourceId) {
                    $query->where('transaction_id', $sourceId);
                }
                if ($paymongoPaymentId) {
                    $query->orWhere('gateway_transaction_id', $paymongoPaymentId);
                }
            })->first();

            if ($payment) {
                $this->markPaymentFailed($payment, data_get($event, 'data.attributes.data'));
            }
        }

        return true;
    }

    protected function getSource(string $sourceId): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/sources/{$sourceId}");

        if ($response->failed()) {
            Log::error('PayMongo source fetch failed', [
                'source_id' => $sourceId,
                'response' => $response->json(),
            ]);
            throw new \Exception('Failed to verify PayMongo source.');
        }

        return (array) $response->json('data');
    }

    protected function getCheckoutSession(string $sessionId): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/checkout_sessions/{$sessionId}");

        if ($response->failed()) {
            Log::error('PayMongo checkout session fetch failed', [
                'session_id' => $sessionId,
                'response' => $response->json(),
            ]);
            throw new \Exception('Failed to verify PayMongo checkout session.');
        }

        return (array) $response->json('data');
    }

    protected function createPaymentFromSource(string $sourceId, int $amount, string $currency = 'PHP'): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/payments", [
                'data' => [
                    'attributes' => [
                        'amount' => $amount,
                        'currency' => strtoupper($currency),
                        'source' => [
                            'id' => $sourceId,
                            'type' => 'source',
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            Log::error('PayMongo payment creation from source failed', [
                'source_id' => $sourceId,
                'response' => $response->json(),
            ]);
            throw new \Exception('Failed to create PayMongo payment from source.');
        }

        return (array) $response->json('data');
    }

    protected function markPaymentCompleted(Payment $payment, ?array $gatewayData = null): void
    {
        if ($payment->status !== 'completed') {
            $payment->update([
                'status' => 'completed',
                'processed_at' => now(),
                'gateway_transaction_id' => data_get($gatewayData, 'id', $payment->gateway_transaction_id),
                'gateway_response' => array_merge($payment->gateway_response ?? [], $gatewayData ? ['payment' => $gatewayData] : []),
            ]);
        }

        $order = $payment->order;
        app(\App\Services\CheckoutService::class)->finalizeOrderPlacement($order);
        $order = $order->fresh();

        if ($order->payment_status !== 'paid' || $order->status === 'pending') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);
        }
    }

    protected function markPaymentFailed(Payment $payment, ?array $gatewayData = null): void
    {
        if ($payment->status !== 'failed') {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => array_merge($payment->gateway_response ?? [], $gatewayData ? ['payment' => $gatewayData] : []),
            ]);
        }

        app(\App\Services\CheckoutService::class)->failOrderPayment($payment->order, $payment);
    }
}
