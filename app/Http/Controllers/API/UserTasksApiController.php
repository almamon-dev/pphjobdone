<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Task;
use Illuminate\Http\Request;

class UserTasksApiController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $bookings = Booking::with(['service', 'pricingPlan', 'tasks'])
            ->where('user_id', $userId)
            ->whereIn('status', ['ongoing', 'active', 'pending'])
            ->latest()
            ->get();

        // Auto-generate default roadmap tasks for bookings if none exist yet
        foreach ($bookings as $booking) {
            if ($booking->tasks->count() === 0) {
                $this->createDefaultTasksForBooking($booking);
                $booking->load('tasks');
            }
        }

        $tasks = $bookings->map(function ($booking) {
            $items = $booking->tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'progress' => (float) $task->progress,
                ];
            });

            $overallProgress = $booking->tasks->count() > 0 
                ? $booking->tasks->avg('progress') 
                : 0;

            return [
                'booking_id' => $booking->id,
                'plan_name' => $booking->plan_name,
                'service_title' => $booking->service?->title ?? ($booking->pricingPlan?->name ?? ($booking->plan_name . ' Service')),
                'overall_progress' => (int) round($overallProgress),
                'status' => ucfirst($booking->status ?? 'Active'),
                'payment_status' => ucfirst($booking->payment_status ?? 'Paid'),
                'items' => $items,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'User progress and tasks fetched successfully',
        ]);
    }

    /**
     * Update task progress via API
     */
    public function updateProgress(Request $request, Task $task)
    {
        $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
        ]);

        $progressVal = (float) $request->progress;

        $task->update([
            'progress' => $progressVal,
            'status' => $progressVal >= 100 ? 'completed' : ($progressVal > 0 ? 'ongoing' : 'pending'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task progress updated successfully',
            'task' => $task
        ]);
    }

    /**
     * Helper to populate tasks for a booking strictly from its purchased package features
     */
    private function createDefaultTasksForBooking(Booking $booking)
    {
        $features = [];
        
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
}
