<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\CardException;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Order $order, array $paymentData = [])
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $order->total_amount * 100, // Convert to cents
                'currency' => strtolower($order->currency),
                'payment_method_types' => ['card'],
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                'description' => "Payment for order #{$order->order_number}",
            ]);

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'stripe',
                'payment_gateway' => 'stripe',
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
                'payment_intent_id' => $paymentIntent->id
            ];

        } catch (\Exception $e) {
            Log::error('Payment intent creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Failed to create payment intent: ' . $e->getMessage());
        }
    }

    public function confirmPayment($paymentIntentId, Order $order)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            $payment = Payment::where('transaction_id', $paymentIntentId)
                ->where('order_id', $order->id)
                ->first();

            if (!$payment) {
                throw new \Exception('Payment record not found');
            }

            if ($paymentIntent->status === 'succeeded') {
                $payment->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $paymentIntent->charges->data[0]->id ?? null,
                    'processed_at' => now(),
                    'gateway_response' => array_merge($payment->gateway_response, [
                        'payment_intent' => $paymentIntent->toArray()
                    ])
                ]);

                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);

                // Send order confirmation email
                \Mail::to($order->customer->email)->send(new \App\Mail\OrderConfirmation($order));

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Payment confirmation failed', [
                'payment_intent_id' => $paymentIntentId,
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Payment confirmation failed: ' . $e->getMessage());
        }
    }

    public function refundPayment(Payment $payment, $amount = null)
    {
        try {
            $refundAmount = $amount ? ($amount * 100) : ($payment->amount * 100);
            
            $refund = \Stripe\Refund::create([
                'payment_intent' => $payment->transaction_id,
                'amount' => $refundAmount,
                'metadata' => [
                    'order_id' => $payment->order_id,
                    'reason' => 'requested_by_customer'
                ]
            ]);

            $payment->update([
                'status' => $amount && $amount < $payment->amount ? 'partially_refunded' : 'refunded',
                'gateway_response' => array_merge($payment->gateway_response, [
                    'refund' => $refund->toArray()
                ])
            ]);

            $payment->order()->update([
                'payment_status' => 'refunded',
                'status' => 'refunded'
            ]);

            return $refund;

        } catch (\Exception $e) {
            Log::error('Payment refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Refund failed: ' . $e->getMessage());
        }
    }

    public function handleWebhook($payload, $signature)
    {
        $webhookSecret = config('services.stripe.webhook_secret');
        
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Webhook signature verification failed', ['error' => $e->getMessage()]);
            throw new \Exception('Webhook signature verification failed');
        }

        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event['data']['object']);
                break;
            
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event['data']['object']);
                break;
            
            case 'charge.dispute.created':
                $this->handleChargeDispute($event['data']['object']);
                break;
                
            default:
                Log::info('Unhandled webhook event', ['type' => $event['type']]);
        }

        return true;
    }

    private function handlePaymentSucceeded($paymentIntent)
    {
        $payment = Payment::where('transaction_id', $paymentIntent['id'])->first();
        
        if ($payment && $payment->status === 'pending') {
            $this->confirmPayment($paymentIntent['id'], $payment->order);
        }
    }

    private function handlePaymentFailed($paymentIntent)
    {
        $payment = Payment::where('transaction_id', $paymentIntent['id'])->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => array_merge($payment->gateway_response, [
                    'failure_reason' => $paymentIntent['last_payment_error']['message'] ?? 'Unknown error'
                ])
            ]);

            $payment->order()->update(['payment_status' => 'failed']);

            // Send payment failed notification
            \Mail::to($payment->order->customer->email)->send(new \App\Mail\PaymentFailed($payment->order));
        }
    }

    private function handleChargeDispute($dispute)
    {
        // Handle charge disputes - notify admin
        Log::warning('Charge dispute created', ['dispute_id' => $dispute['id']]);
        
        // Notify administrators
        $adminEmails = \App\Models\AdminUser::where('is_active', true)->pluck('email');
        if ($adminEmails->isNotEmpty()) {
            \Mail::to($adminEmails)->send(new \App\Mail\ChargeDispute($dispute));
        }
    }
}