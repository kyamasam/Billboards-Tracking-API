<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentVerified extends Mailable
{
    use Queueable, SerializesModels;

    public $payment_details;

    /**
     * PaymentVerified constructor.
     * @param $payment_details
     */
    public function __construct($payment_details)
    {
        $this->payment_details= $payment_details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.payment_receipt');
    }
}
