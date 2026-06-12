<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceRequest;
use Illuminate\Http\Request;

class AmbulanceController extends Controller
{
    public function index(Request $request)
    {
        $query = AmbulanceRequest::query()->with('user');

        $result = AmbulanceRequest::handleSearchAndFilters($request, $query, function ($req) {
            return [
                'id' => $req->id,
                'title' => 'Ambulance Req #' . $req->id . ' - ' . $req->patient_name,
                'subtitle' => $req->address . ' | ' . $req->status
            ];
        });

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return view('admin.ambulance.index', [
            'requests' => $result->orderByRaw("FIELD(status, 'pending', 'dispatched', 'resolved', 'cancelled')")->latest()->paginate(15)->withQueryString(),
            'routePrefix' => 'staff',
        ]);
    }

    public function create()
    {
        return view('admin.ambulance.create', [
            'routePrefix' => 'staff',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'address' => 'required|string|max:1000',
            'status' => 'required|in:pending,dispatched,resolved,cancelled',
            'notes' => 'nullable|string',
        ]);

        $data = $validated;
        
        if ($request->status === 'dispatched') {
            $data['dispatched_at'] = now();
            $data['staff_id'] = auth()->id();
        } elseif ($request->status === 'resolved') {
            $data['resolved_at'] = now();
            $data['staff_id'] = auth()->id();
        }

        AmbulanceRequest::create($data);

        return redirect()->route('staff.ambulance.index')->with('success', 'Ambulance request created successfully.');
    }

    public function edit(AmbulanceRequest $ambulance) // using $ambulance to match resource route
    {
        return view('admin.ambulance.edit', [
            'request' => $ambulance,
            'routePrefix' => 'staff',
        ]);
    }

    public function update(Request $request, AmbulanceRequest $ambulance)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'address' => 'required|string|max:1000',
            'status' => 'required|in:pending,dispatched,resolved,cancelled',
            'notes' => 'nullable|string',
        ]);

        $data = $validated;

        if ($request->status === 'dispatched' && $ambulance->status !== 'dispatched') {
            $data['dispatched_at'] = now();
            if (!$ambulance->staff_id) {
                $data['staff_id'] = auth()->id();
            }
        } elseif ($request->status === 'resolved' && $ambulance->status !== 'resolved') {
            $data['resolved_at'] = now();
            if (!$ambulance->staff_id) {
                $data['staff_id'] = auth()->id();
            }
        }

        $ambulance->update($data);

        if ($ambulance->user) {
            if ($request->status === 'dispatched' && $ambulance->getOriginal('status') !== 'dispatched') {
                $ambulance->user->notify(new \App\Notifications\SystemNotification(
                    'Ambulance Dispatched',
                    "Your ambulance request has been dispatched. Driver is on the way.",
                    'important',
                    null
                ));
            }
        }

        return back()->with('success', 'Ambulance request updated successfully.');
    }

    public function destroy(AmbulanceRequest $ambulance)
    {
        $ambulance->delete();
        return redirect()->route('staff.ambulance.index')->with('success', 'Ambulance request deleted successfully.');
    }
}
