<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;
use App\Notifications\WelcomeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeEmail implements ShouldQueue
{
    public function handle(CustomerRegistered $event): void
    {
        $event->customer->notify(new WelcomeNotification($event->customer));
    }
}
