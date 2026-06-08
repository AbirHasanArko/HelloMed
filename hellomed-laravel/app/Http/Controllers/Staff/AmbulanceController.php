<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceRequest;
use Illuminate\Http\Request;

class AmbulanceController extends Controller
{
    public function index()
    {
        $requests = AmbulanceRequest::orderByRaw("
            CASE status
                WHEN 'pending' THEN 1
                WHEN 'dispatched' THEN 2
                WHEN 'resolved' THEN 3
                WHEN 'cancelled' THEN 4
                ELSE 5
            END
        ")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.ambulance.index', compact('requests'));
    }

    public function update(Request $request, AmbulanceRequest $ambulanceRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,dispatched,resolved,cancelled',
            'notes' => 'nullable|string',
        ]);

        $data = [
            'status' => $request->status,
            'notes' => $request->notes,
        ];

        if ($request->status === 'dispatched' && $ambulanceRequest->status !== 'dispatched') {
            $data['dispatched_at'] = now();
            $data['staff_id'] = auth()->id();
        } elseif ($request->status === 'resolved' && $ambulanceRequest->status !== 'resolved') {
            $data['resolved_at'] = now();
            if (!$ambulanceRequest->staff_id) {
                $data['staff_id'] = auth()->id();
            }
        }

        $ambulanceRequest->update($data);

        return back()->with('success', 'Ambulance request updated successfully.');
    }
}
