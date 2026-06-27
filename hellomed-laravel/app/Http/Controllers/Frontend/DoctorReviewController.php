<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DoctorReviewController extends Controller
{
    public function store(Request $request, Doctor $doctor): RedirectResponse
    {
        abort_unless($request->user()?->role === 'patient', 403);

        // Ensure the patient has at least one completed appointment with this doctor.
        $hasCompleted = Appointment::where('doctor_id', $doctor->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'completed')
            ->exists();

        abort_unless($hasCompleted, 403, 'You can only review doctors you have had a completed appointment with.');

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = $doctor->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        AuditLogger::log('doctor.review_submitted', $doctor, [], [
            'review_id' => $review->id,
            'rating' => $review->rating,
        ]);

        if ($doctor->user) {
            $doctor->user->notify(new \App\Notifications\SystemNotification(
                'New Review Received',
                "{$request->user()->name} left a {$review->rating}-star rating.",
                'normal',
                route('doctors.show', $doctor)
            ));
        }

        return back()->with('status', 'Doctor rating submitted successfully.');
    }
}
