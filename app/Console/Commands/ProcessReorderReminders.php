<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Services\EmailService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ProcessReorderReminders extends Command
{
    protected $signature = 'emails:reorder-reminders {--days=30 : Days since last order to trigger reminder}';
    protected $description = 'Send reorder reminder emails to customers';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Finding customers who haven't ordered in {$days} days...");

        $customers = Customer::whereHas('orders', function ($query) use ($cutoffDate) {
            $query->where('payment_status', 'paid')
                  ->where('created_at', '<', $cutoffDate)
                  ->whereNotExists(function ($subQuery) use ($cutoffDate) {
                      $subQuery->from('orders as recent_orders')
                               ->whereColumn('recent_orders.customer_id', 'orders.customer_id')
                               ->where('recent_orders.created_at', '>', $cutoffDate)
                               ->where('recent_orders.payment_status', 'paid');
                  });
        })->whereHas('preferences', function ($query) {
            $query->where('marketing_emails', true);
        })->get();

        $count = 0;

        foreach ($customers as $customer) {
            $this->emailService->sendReorderReminder($customer);
            $count++;
            $this->line("Sent reorder reminder to: {$customer->email}");
        }

        $this->info("Sent {$count} reorder reminder emails.");
        return 0;
    }
}