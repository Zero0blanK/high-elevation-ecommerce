<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\App;

class PaymentGatewayFactory
{
    /**
     * Create a payment gateway instance
     */
    public static function create(string $gateway): PaymentGatewayInterface
    {
        switch ($gateway) {
            case 'paypal':
                return App::make(PayPalGateway::class);
            case 'paymongo':
            case 'gcash':
            case 'paymongo_card':
                return App::make(PayMongoGateway::class);
            default:
                throw new \InvalidArgumentException("Unsupported payment gateway: {$gateway}");
        }
    }
}
