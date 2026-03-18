<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShipped extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Order $order
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Your Order Has Shipped — #{$this->order->order_number}")
            ->greeting("Good news, {$notifiable->first_name}!")
            ->line("Your order #{$this->order->order_number} is on its way.");

        if ($this->order->tracking_number) {
            $message->line("Tracking Number: {$this->order->tracking_number}");
        }

        return $message
            ->action('Track Order', url("/account/orders/{$this->order->id}"))
            ->line('Thank you for shopping with us!');
    }
}
