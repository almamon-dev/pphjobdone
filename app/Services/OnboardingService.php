<?php

namespace App\Services;

use App\Mail\ClientOnboardingMail;
use App\Models\Booking;
use App\Models\OnboardingWorkflow;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OnboardingService
{
    /**
     * Create or retrieve onboarding workflow for a user/booking
     */
    public function initializeWorkflow(User $user, ?Booking $booking = null): OnboardingWorkflow
    {
        $defaultSteps = [
            ['id' => 'account_setup', 'title' => 'Complete Profile & Account Setup', 'status' => 'completed', 'required' => true],
            ['id' => 'connect_website', 'title' => 'Submit Website Details & Requirements', 'status' => $booking?->website_url ? 'completed' : 'pending', 'required' => true],
            ['id' => 'strategy_call', 'title' => 'Schedule Kick-off Strategy Sync', 'status' => 'pending', 'required' => false],
            ['id' => 'audit_review', 'title' => 'Review Initial AI Site Audit & Strategy', 'status' => 'pending', 'required' => true],
            ['id' => 'milestones_approval', 'title' => 'Approve Project Deliverable Roadmap', 'status' => 'pending', 'required' => true],
        ];

        $completedCount = count(array_filter($defaultSteps, fn($s) => $s['status'] === 'completed'));
        $progress = round(($completedCount / count($defaultSteps)) * 100);

        $workflow = OnboardingWorkflow::firstOrCreate(
            [
                'user_id' => $user->id,
                'booking_id' => $booking?->id,
            ],
            [
                'title' => ($booking?->service?->title ?? 'Client') . ' Onboarding Workflow',
                'status' => 'in_progress',
                'steps' => $defaultSteps,
                'progress_percentage' => $progress,
            ]
        );

        // Auto-generate deliverable tasks for the booking
        if ($booking) {
            $this->createDefaultTasksForBooking($booking);
        }

        // Send Onboarding Email
        try {
            Mail::to($user->email)->send(new ClientOnboardingMail($user, $workflow));
        } catch (\Exception $e) {
            Log::error('Failed to send Client Onboarding Email: ' . $e->getMessage());
        }

        return $workflow;
    }

    /**
     * Populate tasks for a booking from its purchased package features
     */
    public function createDefaultTasksForBooking(Booking $booking): void
    {
        if ($booking->tasks()->count() > 0) {
            return;
        }

        $features = [];
        $booking->loadMissing(['pricingPlan', 'service', 'campaignTier']);
        
        if ($booking->pricingPlan && !empty($booking->pricingPlan->features)) {
            $features = $booking->pricingPlan->features;
        } elseif ($booking->service && !empty($booking->service->features)) {
            $features = $booking->service->features;
        } elseif ($booking->campaignTier && !empty($booking->campaignTier->features)) {
            $features = $booking->campaignTier->features;
        }

        if (is_string($features)) {
            $decoded = json_decode($features, true);
            if (is_array($decoded)) {
                $features = $decoded;
            }
        }

        if (!empty($features) && is_array($features)) {
            foreach ($features as $index => $feature) {
                $title = is_array($feature) 
                    ? ($feature['title'] ?? ('Package Feature ' . ($index + 1))) 
                    : (is_string($feature) ? $feature : 'Deliverable Feature');
                
                $desc = is_array($feature) 
                    ? ($feature['description'] ?? (isset($feature['subItems']) && is_array($feature['subItems']) ? implode(' • ', $feature['subItems']) : 'Deliverable feature included in purchased package.')) 
                    : 'Deliverable feature included in purchased package.';
                
                $booking->tasks()->create([
                    'title' => $title,
                    'description' => $desc,
                    'progress' => 0,
                    'status' => 'pending',
                ]);
            }
        }
    }

    /**
     * Update an onboarding step status
     */
    public function updateStepStatus(OnboardingWorkflow $workflow, string $stepId, string $status): OnboardingWorkflow
    {
        $steps = $workflow->steps ?? [];
        $updated = false;

        foreach ($steps as &$step) {
            if ($step['id'] === $stepId) {
                $step['status'] = $status;
                $updated = true;
                break;
            }
        }

        if ($updated) {
            $completedCount = count(array_filter($steps, fn($s) => $s['status'] === 'completed'));
            $progress = round(($completedCount / count($steps)) * 100);
            $workflowStatus = $progress >= 100 ? 'completed' : 'in_progress';

            $workflow->update([
                'steps' => $steps,
                'progress_percentage' => $progress,
                'status' => $workflowStatus,
                'completed_at' => $progress >= 100 ? now() : null,
            ]);
        }

        return $workflow;
    }
}
