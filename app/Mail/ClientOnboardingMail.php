<?php

namespace App\Mail;

use App\Models\OnboardingWorkflow;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientOnboardingMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public OnboardingWorkflow $workflow;

    public function __construct(User $user, OnboardingWorkflow $workflow)
    {
        $this->user = $user;
        $this->workflow = $workflow;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to PPHJobDone! Let\'s Get Started On Your Onboarding',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.onboarding',
        );
    }
}
