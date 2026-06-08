<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Article;
use App\Models\Doctor;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingAppointments = Appointment::query()->where('status', 'pending')->count();
        $todayAppointments = Appointment::query()->whereDate('scheduled_for', now()->toDateString())->count();
        $doctorCount = Doctor::query()->where('is_active', true)->count();
        $publishedArticles = Article::query()->where('is_published', true)->count();
        $pendingAmbulance = \App\Models\AmbulanceRequest::where('status', 'pending')->count();

        return view('staff.dashboard', compact(
            'pendingAppointments',
            'todayAppointments',
            'doctorCount',
            'publishedArticles',
            'pendingAmbulance'
        ));
    }
}
