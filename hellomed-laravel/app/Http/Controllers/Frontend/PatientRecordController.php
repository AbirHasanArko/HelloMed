<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PatientRecordController extends Controller
{
    public function index(): View
    {
        $user = request()->user();
        abort_unless($user && $user->role === 'patient', 403);

        return view('patient.records', [
            'appointments' => $user->appointments()
                ->with('doctor.department')
                ->latest('scheduled_for')
                ->get(),
            'medicineOrders' => $user->medicineOrders()
                ->with('items.medicine')
                ->latest()
                ->get(),
            'profile' => $user->patientProfile,
        ]);
    }
}
