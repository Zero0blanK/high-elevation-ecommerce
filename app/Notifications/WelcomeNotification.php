<?php

namespace App\Notifications;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Customer $customer
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('ecommerce.store.name', 'High Elevation Coffee'))
            ->greeting("Welcome, {$this->customer->first_name}!")
            ->line('Thank you for creating an account with us.')
            ->line('Explore our premium coffee beans sourced from the highest elevations around the world.')
            ->action('Browse Products', url('/products'))
            ->line('Happy brewing!');
    }
}
