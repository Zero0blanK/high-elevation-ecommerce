<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Payment $payment,
        protected Order $order
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Payment Received — #{$this->order->order_number}")
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("We've received your payment of $" . number_format($this->payment->amount, 2) . ".")
            ->line("Order: #{$this->order->order_number}")
            ->action('View Order', url("/account/orders/{$this->order->id}"))
            ->line('Your order is now being processed.');
    }
}
