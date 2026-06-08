<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function show(Doctor $doctor)
    {
        $upcomingAppointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_for', '>=', now())
            ->where('scheduled_for', '<', now()->addDays(14))
            ->orderBy('scheduled_for')
            ->get(['scheduled_for', 'scheduled_end'])
            ->map(function ($apt) {
                return [
                    'start' => $apt->scheduled_for->format('Y-m-d H:i:s'),
                    'start_formatted' => $apt->scheduled_for->format('M d, Y h:i A'),
                    'end_formatted' => $apt->scheduled_end ? $apt->scheduled_end->format('h:i A') : '',
                ];
            });

        return response()->json([
            'online_available' => $doctor->online_available,
            'online_days' => $doctor->online_available_days ?: $doctor->available_days ?: [],
            'online_from' => $doctor->online_available_from ?: $doctor->available_from,
            'online_to' => $doctor->online_available_to ?: $doctor->available_to,
            
            'offline_available' => $doctor->offline_available,
            'offline_days' => $doctor->offline_available_days ?: $doctor->available_days ?: [],
            'offline_from' => $doctor->offline_available_from ?: $doctor->available_from,
            'offline_to' => $doctor->offline_available_to ?: $doctor->available_to,
            
            'slot_minutes' => $doctor->slot_minutes ?: 30,
            
            'booked_slots' => $upcomingAppointments,
        ]);
    }
}
