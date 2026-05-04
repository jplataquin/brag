<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\PaymentComment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentCommentUserNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $comment;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, PaymentComment $comment)
    {
        $this->payment = $payment;
        $this->comment = $comment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Update on your Manual Payment - ' . $this->payment->reference,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_comment_user',
        );
    }
}
