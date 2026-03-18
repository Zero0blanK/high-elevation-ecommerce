<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Services\InventoryService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateInventoryOnOrder implements ShouldQueue
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->load('items');

        foreach ($order->items as $item) {
            $this->inventoryService->adjustStock(
                $item->product_id,
                -$item->quantity,
                'sale',
                $order,
                "Order #{$order->order_number}"
            );
        }
    }
}
