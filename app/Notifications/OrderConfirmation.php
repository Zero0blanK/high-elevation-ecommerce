<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification implements ShouldQueue
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
            ->subject("Order Confirmed — #{$this->order->order_number}")
            ->greeting("Thank you for your order, {$notifiable->first_name}!")
            ->line("Your order #{$this->order->order_number} has been placed successfully.")
            ->line("Total: $" . number_format($this->order->total_amount, 2))
            ->action('View Order', url("/account/orders/{$this->order->id}"))
            ->line('We will notify you when your order ships.');
    }
}
