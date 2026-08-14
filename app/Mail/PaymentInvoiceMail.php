<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load(['service', 'pricingPlan', 'campaignTier.campaign', 'user']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Confirmation & Official Invoice - BKG-#' . $this->booking->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_invoice',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.invoice', ['booking' => $this->booking]);
        $pdfContent = $pdf->output();

        return [
            Attachment::fromData(fn () => $pdfContent, 'Invoice-INV-BKG-' . $this->booking->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
