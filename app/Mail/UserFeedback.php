<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserFeedback extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subjectLine;
    public $feedbackMessage;
    public $guestEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(?User $user, string $subject, string $message, ?string $guestEmail = null)
    {
        $this->user = $user;
        $this->subjectLine = $subject;
        $this->feedbackMessage = $message;
        $this->guestEmail = $guestEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $replyToEmail = $this->user ? $this->user->email : $this->guestEmail;

        return new Envelope(
            subject: '[BRAG FEEDBACK] ' . $this->subjectLine,
            replyTo: $replyToEmail,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user-feedback',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
