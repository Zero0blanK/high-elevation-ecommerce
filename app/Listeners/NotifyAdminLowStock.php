<?php

namespace App\Listeners;

use App\Events\LowStockAlert;
use App\Notifications\LowStockNotification;
use App\Models\Admin;

class NotifyAdminLowStock
{
    public function handle(LowStockAlert $event): void
    {
        $admins = Admin::where('is_active', true)
            ->whereIn('role', ['super_admin', 'admin', 'manager'])
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new LowStockNotification($event->product, $event->currentStock));
        }
    }
}
