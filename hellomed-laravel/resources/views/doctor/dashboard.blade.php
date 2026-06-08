@extends('layouts.app')

@section('content')
<section class="section">
    <h1>Doctor dashboard</h1>
    <div class="meta-row" style="margin-bottom:16px;">
        <a class="ghost-button" href="{{ route('doctor.articles.index') }}">My articles</a>
        <a class="ghost-button" href="{{ route('doctor.articles.create') }}">Write article</a>
    </div>

    <div class="grid cols-2">
        <div class="card">
            <h3>My schedule settings</h3>
            <form method="POST" action="{{ route('doctor.schedule.update') }}">
                @csrf
                @method('PATCH')

                <label>
                    Clinic / chamber address
                    <input type="text" name="clinic_address" value="{{ old('clinic_address', $doctor->clinic_address) }}">
                </label>

                <label><input type="checkbox" name="online_available" value="1" @checked(old('online_available', $doctor->online_available))> Online consultation enabled</label>
                <label>
                    Online days
                    <select name="online_available_days[]" multiple size="7">
                        @foreach (['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                            <option value="{{ $day }}" @selected(in_array($day, old('online_available_days', $doctor->online_available_days ?? []), true))>{{ ucfirst($day) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Online from
                    <input type="time" name="online_available_from" value="{{ old('online_available_from', $doctor->online_available_from) }}">
                </label>
                <label>
                    Online to
                    <input type="time" name="online_available_to" value="{{ old('online_available_to', $doctor->online_available_to) }}">
                </label>

                <label><input type="checkbox" name="offline_available" value="1" @checked(old('offline_available', $doctor->offline_available))> Chamber visit enabled</label>
                <label>
                    Offline days
                    <select name="offline_available_days[]" multiple size="7">
                        @foreach (['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                            <option value="{{ $day }}" @selected(in_array($day, old('offline_available_days', $doctor->offline_available_days ?? []), true))>{{ ucfirst($day) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Offline from
                    <input type="time" name="offline_available_from" value="{{ old('offline_available_from', $doctor->offline_available_from) }}">
                </label>
                <label>
                    Offline to
                    <input type="time" name="offline_available_to" value="{{ old('offline_available_to', $doctor->offline_available_to) }}">
                </label>

                <label>
                    Slot minutes
                    <select name="slot_minutes" required>
                        @foreach ([15,20,30,45,60] as $slot)
                            <option value="{{ $slot }}" @selected((int) old('slot_minutes', $doctor->slot_minutes) === $slot)>{{ $slot }}</option>
                        @endforeach
                    </select>
                </label>

                <button class="button" type="submit">Save schedule</button>
            </form>

            <hr style="margin:20px 0; border:0; border-top:1px solid var(--border);">

            <h3>Change login password</h3>
            <form method="POST" action="{{ route('doctor.password.update') }}">
                @csrf
                @method('PATCH')
                <label>
                    Current password
                    <input type="password" name="current_password" required>
                </label>
                <label>
                    New password
                    <input type="password" name="new_password" required>
                </label>
                <label>
                    Confirm new password
                    <input type="password" name="new_password_confirmation" required>
                </label>
                <button class="ghost-button" type="submit">Update password</button>
            </form>
        </div>

        <div class="card">
            <h3>Next 30 days calendar snapshot</h3>
            <div class="list" style="margin-bottom: 14px;">
                @forelse ($calendarSummary as $entry)
                    <div class="list-item">
                        <strong>{{ \Illuminate\Support\Carbon::parse($entry->appointment_date)->format('M d, Y') }}</strong>
                        <p>{{ $entry->total }} appointment(s)</p>
                    </div>
                @empty
                    <div class="list-item">No upcoming appointments in calendar range.</div>
                @endforelse
            </div>

            <h3>All appointments (including past visits)</h3>
            <div class="pill-row" style="margin-bottom: 12px;">
                <a class="{{ $appointmentFilter === 'today' ? 'button' : 'ghost-button' }}" href="{{ route('doctor.dashboard', ['appointment_filter' => 'today']) }}">Today</a>
                <a class="{{ $appointmentFilter === 'next' ? 'button' : 'ghost-button' }}" href="{{ route('doctor.dashboard', ['appointment_filter' => 'next']) }}">Next appointments</a>
                <a class="{{ $appointmentFilter === 'past' ? 'button' : 'ghost-button' }}" href="{{ route('doctor.dashboard', ['appointment_filter' => 'past']) }}">Past appointments</a>
                <a class="{{ $appointmentFilter === 'all' ? 'button' : 'ghost-button' }}" href="{{ route('doctor.dashboard', ['appointment_filter' => 'all']) }}">All appointments</a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Mode</th>
                        <th>When</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->patient_name }}</td>
                            <td>{{ ucfirst($appointment->service_mode) }}</td>
                            <td>{{ $appointment->scheduled_for?->format('M d, Y h:i A') }}</td>
                            <td>{{ ucfirst($appointment->status) }}</td>
                            <td><a class="ghost-button" href="{{ route('doctor.appointments.show', $appointment) }}">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">No appointments found for selected filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div style="margin-top:20px;">{{ $appointments->links() }}</div>
        </div>
    </div>
</section>
@endsection
