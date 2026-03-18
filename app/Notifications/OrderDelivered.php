<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderDelivered extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject("Order Delivered — #{$this->order->order_number}")
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("Your order #{$this->order->order_number} has been delivered.")
            ->action('View Order', url("/account/orders/{$this->order->id}"))
            ->line('We hope you enjoy your coffee! Consider leaving a review.');
    }
}
