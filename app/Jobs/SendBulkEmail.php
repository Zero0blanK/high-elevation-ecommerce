<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendBulkEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        protected array $customerIds,
        protected string $subject,
        protected string $content
    ) {}

    public function handle(): void
    {
        $customers = Customer::whereIn('id', $this->customerIds)
            ->whereHas('preferences', fn($q) => $q->where('marketing_emails', true))
            ->get();

        foreach ($customers as $customer) {
            \Illuminate\Support\Facades\Mail::raw($this->content, function ($message) use ($customer) {
                $message->to($customer->email)
                    ->subject($this->subject);
            });
        }
    }
}
