@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="grid cols-2 fade-in">
            <div class="auth-sidebar">
                <div class="auth-pattern"></div>
                <div style="position:relative;z-index:1;">
                    <div class="tag">Book appointment</div>
                    <h1 style="font-size:1.8rem;">{{ $doctor->name }}</h1>
                    <p style="font-size:15px;">{{ $doctor->department?->name }} · {{ $doctor->specialty }}</p>
                    <p>Choose online or offline care and send an appointment request.</p>
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1" opacity="0.3" style="margin-top:16px;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
            </div>
            <div class="card">
                <h2 style="margin-bottom:24px;">Appointment details</h2>
                <form method="POST" action="{{ route('appointments.store') }}">
                    @csrf
                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                    <input type="hidden" name="department_id" value="{{ $doctor->department_id }}">

                    <label>
                        Patient name
                        <input type="text" name="patient_name" value="{{ old('patient_name', auth()->user()?->name) }}" required>
                    </label>
                    <label>
                        Email
                        <input type="email" name="patient_email" value="{{ old('patient_email', auth()->user()?->email) }}" required>
                    </label>
                    <label>
                        Phone
                        <input type="text" name="patient_phone" value="{{ old('patient_phone') }}" required>
                    </label>
                    <label>
                        Service mode
                        <select name="service_mode" required>
                            <option value="online" @selected(old('service_mode') === 'online')>Online</option>
                            <option value="offline" @selected(old('service_mode') === 'offline')>Offline</option>
                        </select>
                    </label>
                    <label>
                        Preferred time
                        <input type="datetime-local" name="scheduled_for" value="{{ old('scheduled_for') }}" required>
                    </label>
                    <label>
                        Optional payment method
                        <select name="payment_method">
                            <option value="none" @selected(old('payment_method', 'none') === 'none')>Pay later at hospital</option>
                            <option value="bkash" @selected(old('payment_method') === 'bkash')>bKash</option>
                            <option value="nagad" @selected(old('payment_method') === 'nagad')>Nagad</option>
                            <option value="cash-counter" @selected(old('payment_method') === 'cash-counter')>Hospital cash counter</option>
                        </select>
                    </label>
                    <label>
                        Reason for visit
                        <textarea name="reason" required>{{ old('reason') }}</textarea>
                    </label>
                    <label>
                        Additional notes
                        <textarea name="notes">{{ old('notes') }}</textarea>
                    </label>
                    <button class="button" type="submit" style="width:100%;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>
                        Submit request
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
