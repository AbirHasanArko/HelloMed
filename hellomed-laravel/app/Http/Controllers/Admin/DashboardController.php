<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Appointment;
use App\Models\Article;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $since = now()->subDay();

        $recentLogs = AuditLog::query()
            ->where('created_at', '>=', $since)
            ->get(['action', 'entity_type', 'entity_id', 'meta']);

        $failedLoginCount = $recentLogs
            ->where('action', 'auth.login_failed')
            ->count();

        $failedPaymentCallbackCount = $recentLogs
            ->filter(function ($log): bool {
                return $log->action === 'medicine_order.payment_callback'
                    && (($log->meta['callback_status'] ?? null) === 'failed');
            })
            ->count();

        $frequentAppointmentStatusChanges = $recentLogs
            ->where('action', 'appointment.status_updated')
            ->groupBy(fn ($log) => $log->entity_type.'#'.$log->entity_id)
            ->filter(fn ($group) => $group->count() >= 3)
            ->count();

        return view('admin.dashboard', [
            'doctorCount' => Doctor::query()->count(),
            'departmentCount' => Department::query()->count(),
            'appointmentCount' => Appointment::query()->count(),
            'articleCount' => Article::query()->count(),
            'paymentCount' => Payment::query()->count(),
            'failedLoginCount' => $failedLoginCount,
            'failedPaymentCallbackCount' => $failedPaymentCallbackCount,
            'frequentAppointmentStatusChanges' => $frequentAppointmentStatusChanges,
        ]);
    }
}
