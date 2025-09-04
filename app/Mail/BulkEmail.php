<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $customer;
    public $emailSubject;
    public $emailContent;
    public $templateName;

    public function __construct(Customer $customer, $subject, $content, $templateName = null)
    {
        $this->customer = $customer;
        $this->emailSubject = $subject;
        $this->emailContent = $content;
        $this->templateName = $templateName;
    }

    public function build()
    {
        $view = $this->templateName ? 'emails.templates.' . $this->templateName : 'emails.customer.bulk_email';
        
        return $this->subject($this->emailSubject)
            ->view($view)
            ->with([
                'customer' => $this->customer,
                'content' => $this->emailContent,
                'unsubscribeUrl' => route('newsletter.unsubscribe', ['token' => $this->customer->id])
            ]);
    }
}