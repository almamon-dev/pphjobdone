<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactApiController extends Controller
{
    /**
     * Store a new contact message
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        $message = \App\Models\ContactMessage::create($validatedData);

        // Auto Sync Lead to CRM Database
        try {
            \App\Models\Lead::updateOrCreate(
                ['email' => $validatedData['email']],
                [
                    'name' => trim(($validatedData['first_name'] ?? '') . ' ' . ($validatedData['last_name'] ?? '')),
                    'phone' => $validatedData['phone_number'] ?? null,
                    'service_interest' => 'Contact Form Inquiry',
                    'qualification_status' => 'Warm',
                    'qualification_summary' => $validatedData['message'],
                    'status' => 'new',
                ]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Contact Lead Sync Warning: ' . $e->getMessage());
        }

        // Notify all admins
        $admins = \App\Models\User::where('is_admin', true)->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewContactMessageNotification($message));

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your message! We will get back to you soon.',
            'data' => $message,
        ], 201);
    }
}
