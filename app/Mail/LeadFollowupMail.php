<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadFollowupMail extends Mailable
{
    use Queueable, SerializesModels;

    public Lead $lead;
    public string $customMessage;

    public function __construct(Lead $lead, string $customMessage = '')
    {
        $this->lead = $lead;
        $this->customMessage = $customMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Following up on your interest with PPHJobDone - Next Steps for ' . ($this->lead->company_name ?? 'Your Business'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-followup',
        );
    }
}
