<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DoctorReviewController extends Controller
{
    public function store(Request $request, Doctor $doctor): RedirectResponse
    {
        abort_unless($request->user()?->role === 'patient', 403);

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

        return back()->with('status', 'Doctor rating submitted successfully.');
    }
}
