@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding: 0 0 16px;">
        <div>
            <h1>Audit Logs</h1>
            <p>Track important operational actions across appointments, prescriptions, pharmacy, and content workflows.</p>
        </div>
    </div>

    <div class="card" style="margin-bottom: 16px;">
        <form method="GET" class="grid cols-4">
            <label>
                Action contains
                <input type="text" name="action" value="{{ request('action') }}" placeholder="appointment.status_updated">
            </label>
            <label>
                Entity type
                <select name="entity_type">
                    <option value="">All</option>
                    @foreach ($entityTypes as $entityType)
                        <option value="{{ $entityType }}" @selected(request('entity_type') === $entityType)>{{ $entityType }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <input type="checkbox" name="critical_only" value="1" @checked(request('critical_only'))>
                Critical events only
            </label>
            <div style="display:flex; align-items:end; gap:8px;">
                <button class="button" type="submit">Filter</button>
                <a class="ghost-button" href="{{ route('admin.audit-logs.export', request()->query()) }}">Export CSV</a>
                <a class="ghost-button" href="{{ route('admin.audit-logs.index') }}">Reset</a>
            </div>
        </form>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Actor</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>Changes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                        <td>{{ $log->actor?->name ?? 'System' }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->entity_type }}#{{ $log->entity_id }}</td>
                        <td>
                            @if ($log->old_values || $log->new_values)
                                <details>
                                    <summary>View</summary>
                                    @if ($log->old_values)
                                        <p><strong>Old:</strong></p>
                                        <pre style="white-space: pre-wrap;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                    @endif
                                    @if ($log->new_values)
                                        <p><strong>New:</strong></p>
                                        <pre style="white-space: pre-wrap;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                    @endif
                                </details>
                            @else
                                <span class="muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No audit logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px;">{{ $logs->links() }}</div>
    </div>
</section>
@endsection
