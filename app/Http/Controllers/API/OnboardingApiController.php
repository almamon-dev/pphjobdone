<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OnboardingWorkflow;
use App\Services\OnboardingService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OnboardingApiController extends Controller
{
    use ApiResponse;

    protected OnboardingService $onboardingService;

    public function __construct(OnboardingService $onboardingService)
    {
        $this->onboardingService = $onboardingService;
    }

    /**
     * Get or initialize onboarding status for logged in user
     */
    public function getStatus(Request $request)
    {
        $user = auth()->user();
        $latestBooking = \App\Models\Booking::where('user_id', $user->id)->latest()->first();

        $workflow = OnboardingWorkflow::where('user_id', $user->id)->first();

        if (!$workflow) {
            $workflow = $this->onboardingService->initializeWorkflow($user, $latestBooking);
        }

        return $this->sendResponse([
            'id' => $workflow->id,
            'title' => $workflow->title,
            'status' => $workflow->status,
            'progress_percentage' => $workflow->progress_percentage,
            'steps' => $workflow->steps,
            'completed_at' => $workflow->completed_at,
        ], 'Onboarding workflow fetched successfully');
    }

    /**
     * Complete an onboarding step
     */
    public function updateStep(Request $request)
    {
        $request->validate([
            'step_id' => 'required|string',
            'status' => 'required|in:pending,completed',
        ]);

        $user = auth()->user();
        $workflow = OnboardingWorkflow::where('user_id', $user->id)->firstOrFail();

        $updatedWorkflow = $this->onboardingService->updateStepStatus($workflow, $request->step_id, $request->status);

        return $this->sendResponse($updatedWorkflow, 'Onboarding step updated successfully');
    }
}
