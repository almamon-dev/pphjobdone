<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('user', 'service')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings
        ]);
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'service', 'tasks' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => $booking
        ]);
    }

    public function storeTask(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'progress' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:pending,ongoing,completed',
            'due_date' => 'nullable|date',
        ]);

        $booking->tasks()->create($validated);

        return back()->with('success', 'Task created successfully.');
    }

    public function updateTask(Request $request, Booking $booking, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'progress' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:pending,ongoing,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return back()->with('success', 'Task updated successfully.');
    }

    public function destroyTask(Booking $booking, Task $task)
    {
        $task->delete();

        return back()->with('success', 'Task deleted successfully.');
    }
}
