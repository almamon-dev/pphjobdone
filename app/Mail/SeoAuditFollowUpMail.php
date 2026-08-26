<?php

namespace App\Mail;

use App\Models\SeoAudit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SeoAuditFollowUpMail extends Mailable
{
    use Queueable, SerializesModels;

    public SeoAudit $audit;
    public int $step;

    /**
     * Create a new message instance.
     */
    public function __construct(SeoAudit $audit, int $step)
    {
        $this->audit = $audit;
        $this->step = $step;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->step) {
            1 => 'Checking in: Any questions about your SEO Audit?',
            2 => 'Let\'s review your SEO recommendations',
            3 => 'Final Follow-up: Ready to boost your SEO rankings?',
            default => 'SEO Audit Follow-up',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.seo-audit-follow-up',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
