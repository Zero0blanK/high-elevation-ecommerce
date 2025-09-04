<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Order;

class ProcessWebhooks extends Command
{
    protected $signature = 'webhooks:process {--retry-failed : Retry failed webhook deliveries}';
    protected $description = 'Process outbound webhooks for order updates';

    public function handle()
    {
        $this->info('Processing webhooks...');

        if ($this->option('retry-failed')) {
            $this->retryFailedWebhooks();
        }

        // Process new order webhooks
        $this->processOrderWebhooks();

        return 0;
    }

    private function processOrderWebhooks()
    {
        // Get orders that need webhook notifications
        $orders = Order::whereIn('status', ['processing', 'shipped', 'delivered'])
            ->where('webhook_sent', false)
            ->orWhereNull('webhook_sent')
            ->limit(50)
            ->get();

        foreach ($orders as $order) {
            $this->sendOrderWebhook($order);
        }
    }

    private function sendOrderWebhook(Order $order)
    {
        $webhookUrl = config('ecommerce.webhook_url');
        
        if (!$webhookUrl) {
            return;
        }

        try {
            $response = Http::timeout(30)->post($webhookUrl, [
                'event' => 'order.status_changed',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'customer_email' => $order->customer->email,
                'total_amount' => $order->total_amount,
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'sku' => $item->product_sku,
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price,
                    ];
                }),
                'timestamp' => now()->toISOString(),
            ]);

            if ($response->successful()) {
                $order->update(['webhook_sent' => true]);
                $this->line("Webhook sent for order: {$order->order_number}");
            } else {
                $this->warn("Webhook failed for order {$order->order_number}: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("Webhook error for order {$order->order_number}: " . $e->getMessage());
        }
    }

    private function retryFailedWebhooks()
    {
        $this->info('Retrying failed webhooks...');
        
        $failedOrders = Order::where('webhook_sent', false)
            ->where('created_at', '>', now()->subDays(7)) // Only retry recent orders
            ->get();

        foreach ($failedOrders as $order) {
            $this->sendOrderWebhook($order);
        }
    }
}