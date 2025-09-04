<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChargeDispute extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $dispute;

    public function __construct($dispute)
    {
        $this->dispute = $dispute;
    }

    public function build()
    {
        return $this->subject('Charge Dispute Alert - ' . $this->dispute['id'])
            ->view('emails.admin.charge_dispute')
            ->with([
                'dispute' => $this->dispute,
                'adminUrl' => route('admin.payments.disputes')
            ]);
    }
}