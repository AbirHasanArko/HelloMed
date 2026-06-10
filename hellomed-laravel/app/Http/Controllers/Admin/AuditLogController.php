<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = $this->buildFilteredQuery($request)
            ->with('actor')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'entityTypes' => AuditLog::query()->select('entity_type')->distinct()->orderBy('entity_type')->pluck('entity_type'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'audit-logs-'.now()->format('Ymd-His').'.csv';

        $callback = function () use ($request): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Time', 'Actor User ID', 'Action', 'Entity Type', 'Entity ID', 'Old Values', 'New Values', 'Meta', 'IP Address']);

            $this->buildFilteredQuery($request)
                ->orderByDesc('created_at')
                ->chunk(500, function ($logs) use ($handle): void {
                    foreach ($logs as $log) {
                        fputcsv($handle, [
                            optional($log->created_at)->toDateTimeString(),
                            $log->actor_user_id,
                            $log->action,
                            $log->entity_type,
                            $log->entity_id,
                            json_encode($log->old_values, JSON_UNESCAPED_UNICODE),
                            json_encode($log->new_values, JSON_UNESCAPED_UNICODE),
                            json_encode($log->meta, JSON_UNESCAPED_UNICODE),
                            $log->ip_address,
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildFilteredQuery(Request $request)
    {
        return AuditLog::query()
            ->when($request->filled('action'), fn ($query) => $query->where('action', 'like', '%'.$request->string('action')->toString().'%'))
            ->when($request->filled('entity_type'), fn ($query) => $query->where('entity_type', $request->string('entity_type')->toString()))
            ->when($request->boolean('critical_only'), function ($query): void {
                $query->where(function ($criticalQuery): void {
                    $criticalQuery
                        ->whereIn('action', [
                            'auth.login_failed',
                            'auth.login_locked',
                            'auth.password_changed',
                            'appointment.status_updated',
                        ])
                        ->orWhere(function ($paymentQuery): void {
                            $paymentQuery
                                ->where('action', 'medicine_order.payment_callback')
                                ->where('meta', 'like', '%"callback_status":"failed"%');
                        });
                });
            });
    }
}
