<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;
use App\Notifications\WelcomeNotification;

class SendWelcomeEmail
{
    public function handle(CustomerRegistered $event): void
    {
        $event->customer->notifyNow(new WelcomeNotification($event->customer));
    }
}
