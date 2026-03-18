<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalGateway implements PaymentGatewayInterface
{
    private string $baseUrl;
    private string $clientId;
    private string $secret;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id', '');
        $this->secret = config('services.paypal.secret', '');
        $this->baseUrl = config('services.paypal.sandbox', true)
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    public function getGatewayName(): string
    {
        return 'paypal';
    }

    public function createPayment(Order $order, array $data = []): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'reference_id' => $order->order_number,
                        'amount' => [
                            'currency_code' => $order->currency,
                            'value' => number_format($order->total_amount, 2, '.', ''),
                        ],
                        'description' => "Order #{$order->order_number}",
                    ]],
                    'application_context' => [
                        'return_url' => route('checkout.success', ['order' => $order->order_number]),
                        'cancel_url' => route('checkout.index'),
                        'brand_name' => config('ecommerce.store.name'),
                    ],
                ]);

            $paypalOrder = $response->json();

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'paypal',
                'payment_gateway' => $this->getGatewayName(),
                'transaction_id' => $paypalOrder['id'],
                'amount' => $order->total_amount,
                'currency' => $order->currency,
                'status' => 'pending',
                'gateway_response' => $paypalOrder,
            ]);

            $approvalUrl = collect($paypalOrder['links'] ?? [])
                ->firstWhere('rel', 'approve')['href'] ?? null;

            return [
                'payment' => $payment,
                'approval_url' => $approvalUrl,
                'paypal_order_id' => $paypalOrder['id'],
            ];
        } catch (\Exception $e) {
            Log::error('PayPal: Payment creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function confirmPayment(string $transactionId, Order $order): bool
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders/{$transactionId}/capture");

            $capture = $response->json();

            $payment = Payment::where('transaction_id', $transactionId)
                ->where('order_id', $order->id)
                ->firstOrFail();

            if (($capture['status'] ?? '') === 'COMPLETED') {
                $captureId = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

                $payment->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $captureId,
                    'processed_at' => now(),
                    'gateway_response' => array_merge($payment->gateway_response ?? [], [
                        'capture' => $capture,
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
            Log::error('PayPal: Confirmation failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function refund(Payment $payment, ?float $amount = null): mixed
    {
        try {
            $accessToken = $this->getAccessToken();
            $captureId = $payment->gateway_transaction_id;

            $body = [];
            if ($amount) {
                $body['amount'] = [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency_code' => $payment->currency,
                ];
            }

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/v2/payments/captures/{$captureId}/refund", $body);

            $refund = $response->json();

            $payment->update([
                'status' => ($amount && $amount < $payment->amount) ? 'partially_refunded' : 'refunded',
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'refund' => $refund,
                ]),
            ]);

            $payment->order->update([
                'payment_status' => 'refunded',
                'status' => 'refunded',
            ]);

            return $refund;
        } catch (\Exception $e) {
            Log::error('PayPal: Refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function handleWebhook(string $payload, string $signature): bool
    {
        $data = json_decode($payload, true);
        $eventType = $data['event_type'] ?? '';

        match ($eventType) {
            'CHECKOUT.ORDER.APPROVED' => $this->handleOrderApproved($data),
            'PAYMENT.CAPTURE.COMPLETED' => $this->handleCaptureCompleted($data),
            default => Log::info('PayPal: Unhandled webhook', ['type' => $eventType]),
        };

        return true;
    }

    private function getAccessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->secret)
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        return $response->json('access_token');
    }

    private function handleOrderApproved(array $data): void
    {
        $paypalOrderId = $data['resource']['id'] ?? null;
        if ($paypalOrderId) {
            $payment = Payment::where('transaction_id', $paypalOrderId)->first();
            if ($payment) {
                $this->confirmPayment($paypalOrderId, $payment->order);
            }
        }
    }

    private function handleCaptureCompleted(array $data): void
    {
        Log::info('PayPal: Capture completed', ['data' => $data]);
    }
}
