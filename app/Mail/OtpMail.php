<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $otp;
    public $user;
    public $purpose;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $user, $purpose)
    {
        $this->otp = $otp;
        $this->user = $user;
        $this->purpose = $purpose;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Verification Code: ' . $this->otp . ' (' . $this->purpose . ')',
            replyTo: [
                config('mail.from.address', 'support@pphjobdone.com')
            ],
            using: [
                function (\Symfony\Component\Mime\Email $email) {
                    $email->getHeaders()->addTextHeader('X-Auto-Response-Suppress', 'OOF, AutoReply');
                    $email->getHeaders()->addTextHeader('X-Mailer', 'PPHJobDone Security System');
                    $email->getHeaders()->addTextHeader('X-Priority', '1 (Highest)');
                }
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            text: 'emails.otp-text',
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
