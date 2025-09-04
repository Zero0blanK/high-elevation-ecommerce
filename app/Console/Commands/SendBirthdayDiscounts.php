<?php

namespace App\Console\Commands;

use App\Services\EmailService;
use Illuminate\Console\Command;

class SendBirthdayDiscounts extends Command
{
    protected $signature = 'emails:birthday-discounts';
    protected $description = 'Send birthday discount emails to customers';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $this->info('Sending birthday discount emails...');
        
        $this->emailService->sendBirthdayDiscounts();
        
        $this->info('Birthday discount emails sent successfully.');
        return 0;
    }
}