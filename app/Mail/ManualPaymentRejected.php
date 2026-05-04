<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ManualPaymentRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, $reason = null)
    {
        $this->payment = $payment;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Manual Payment Rejected - ' . $this->payment->reference,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.manual_payment_rejected',
        );
    }
}
