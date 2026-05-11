<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function createPaymentIntent(Order $order, array $paymentData = []): array
    {
        $gatewayName = $order->payment_method ?? 'paymongo';
        
        try {
            $gateway = PaymentGatewayFactory::create($gatewayName);
            return $gateway->createPayment($order, $paymentData);
        } catch (\Exception $e) {
            Log::error("Failed to create payment intent for gateway: {$gatewayName}", [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function confirmPayment($transactionId, Order $order): bool
    {
        $gatewayName = $order->payment_method ?? 'paymongo';
        
        try {
            $gateway = PaymentGatewayFactory::create($gatewayName);
            return $gateway->confirmPayment($transactionId, $order);
        } catch (\Exception $e) {
            Log::error("Failed to confirm payment for gateway: {$gatewayName}", [
                'transaction_id' => $transactionId,
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function refundPayment(Payment $payment, $amount = null)
    {
        $gatewayName = $payment->payment_gateway ?? 'paymongo';
        
        try {
            $gateway = PaymentGatewayFactory::create($gatewayName);
            return $gateway->refund($payment, $amount);
        } catch (\Exception $e) {
            Log::error("Failed to process refund for gateway: {$gatewayName}", [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function handleWebhook($payload, $signature)
    {
        // For webhooks, we might need to identify the gateway from the payload
        // Or have separate endpoints. Here we assume PayMongo for now as per original code.
        // A better way would be to have gateway-specific webhook controllers.
        
        try {
            $gateway = PaymentGatewayFactory::create('paymongo');
            return $gateway->handleWebhook($payload, $signature);
        } catch (\Exception $e) {
            Log::error("Failed to handle PayMongo webhook", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
