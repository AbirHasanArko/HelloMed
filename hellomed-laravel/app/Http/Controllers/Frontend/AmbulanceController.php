<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceRequest;
use Illuminate\Http\Request;

class AmbulanceController extends Controller
{
    public function create()
    {
        return view('ambulance.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:255',
            'address' => 'required_without:latitude|string|nullable|max:1000',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        AmbulanceRequest::create([
            'user_id' => auth()->id(),
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'status' => 'pending',
        ]);

        return redirect()->route('home')->with('success', 'Ambulance requested successfully! Our team will contact you immediately.');
    }
}
