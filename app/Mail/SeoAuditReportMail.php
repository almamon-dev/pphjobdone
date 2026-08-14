<?php

namespace App\Mail;

use App\Models\SeoAudit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SeoAuditReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public SeoAudit $audit;

    /**
     * Create a new message instance.
     */
    public function __construct(SeoAudit $audit)
    {
        $this->audit = $audit;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your AI SEO Audit Report is Ready!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.seo-audit-report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.seo-audit', ['data' => $this->audit->response_data]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'SEO-Audit-Report.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
