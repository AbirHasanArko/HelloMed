@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Appointments</h1>
        
        <x-search-filter 
            action="{{ route('admin.appointments.index') }}" 
            search-placeholder="Search appointments by patient name, phone, email, doctor name..." 
            :filters="[
                'status' => ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'],
                'appointment_date' => 'date_range'
            ]" 
        />

        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Mode</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appointment)
                        <tr>
                            <td>
                                <strong>{{ $appointment->patient_name }}</strong>
                                <div class="muted" style="font-size: 13px; margin-top: 2px; margin-bottom: 4px;">{{ $appointment->patient_phone }}</div>
                                @if ($appointment->user?->patientProfile)
                                    @php $p = $appointment->user->patientProfile; @endphp
                                    <div class="muted" style="font-size: 12px; margin-top: 4px; line-height: 1.4;">
                                        @if($p->date_of_birth) <div>DOB: {{ $p->date_of_birth->format('M d, Y') }}</div> @endif
                                        @if($p->gender) <div>Gender: {{ $p->gender }}</div> @endif
                                        @if($p->height || $p->weight) <div>{{ $p->height }} / {{ $p->weight }}</div> @endif
                                        @if($p->allergies) <div style="color:var(--error-text);">Allergies: {{ $p->allergies }}</div> @endif
                                        @if($p->known_conditions) <div>Conditions: {{ $p->known_conditions }}</div> @endif
                                        <div style="margin-top: 4px;"><a href="{{ route('admin.patients.edit', $appointment->user) }}" style="text-decoration: underline; color: var(--primary);">Edit Profile</a></div>
                                    </div>
                                @elseif($appointment->user)
                                    <div style="margin-top: 4px;"><a href="{{ route('admin.patients.edit', $appointment->user) }}" style="text-decoration: underline; color: var(--primary); font-size: 12px;">Create Profile</a></div>
                                @endif
                            </td>
                            <td>{{ $appointment->doctor?->name }}</td>
                            <td>{{ $appointment->service_mode }}</td>
                            <td>{{ $appointment->payment_status }}</td>
                            <td>{{ $appointment->status }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status">
                                        @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected($appointment->status === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button class="button" type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 20px;">{{ $appointments->links() }}</div>
        </div>
    </section>
@endsection
