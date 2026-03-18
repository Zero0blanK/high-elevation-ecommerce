<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Product $product,
        protected int $currentStock
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Low Stock Alert: {$this->product->name}")
            ->greeting('Low Stock Alert')
            ->line("Product \"{$this->product->name}\" (SKU: {$this->product->sku}) is running low.")
            ->line("Current stock: {$this->currentStock} units")
            ->line("Threshold: {$this->product->low_stock_threshold} units")
            ->action('Manage Inventory', url("/admin/inventory"))
            ->line('Please restock this product soon.');
    }
}
