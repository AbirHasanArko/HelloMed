@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>My appointments</h1>
        <p>Track your appointment status, service mode, and optional payment state.</p>
        <div class="meta-row" style="margin-bottom: 12px;">
            <a class="ghost-button" href="{{ route('patient.records') }}">Open full health records</a>
        </div>

        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Department</th>
                        <th>Service mode</th>
                        <th>When</th>
                        <th>Appointment</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->doctor?->name }}</td>
                            <td>{{ $appointment->department?->name }}</td>
                            <td>{{ ucfirst($appointment->service_mode) }}</td>
                            <td>{{ $appointment->scheduled_for?->format('M d, Y h:i A') }}</td>
                            <td>{{ ucfirst($appointment->status) }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($appointment->payment_status)) }}</td>
                            <td><a class="ghost-button" href="{{ route('patient.appointments.show', $appointment) }}">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="muted">No appointments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div style="margin-top: 20px;">{{ $appointments->links() }}</div>
        </div>
    </section>
@endsection
