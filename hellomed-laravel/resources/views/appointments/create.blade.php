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
                    
                    <div style="margin-top: 24px; padding: 16px; background: rgba(0,0,0,0.2); border-radius: 8px;">
                        <h3 style="margin-bottom: 12px; font-size: 16px;">Doctor's Schedule</h3>
                        @if($doctor->online_available)
                            <p style="margin-bottom: 8px; font-size: 14px;"><strong>Online:</strong> {{ implode(', ', $doctor->online_available_days ?: $doctor->available_days ?: []) }} ({{ $doctor->online_available_from ?: $doctor->available_from ?: 'Anytime' }} - {{ $doctor->online_available_to ?: $doctor->available_to ?: 'Anytime' }})</p>
                        @endif
                        @if($doctor->offline_available)
                            <p style="margin-bottom: 8px; font-size: 14px;"><strong>Offline:</strong> {{ implode(', ', $doctor->offline_available_days ?: $doctor->available_days ?: []) }} ({{ $doctor->offline_available_from ?: $doctor->available_from ?: 'Anytime' }} - {{ $doctor->offline_available_to ?: $doctor->available_to ?: 'Anytime' }})</p>
                        @endif
                        <p style="font-size: 14px;"><strong>Slot Duration:</strong> {{ $doctor->slot_minutes ?: 30 }} mins</p>
                    </div>

                    @if($upcomingAppointments->isNotEmpty())
                        <div style="margin-top: 16px; padding: 16px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                            <h3 style="margin-bottom: 12px; font-size: 14px; display:flex; align-items:center; gap:6px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Recently Booked Slots (Next 14 Days)
                            </h3>
                            <ul style="font-size: 13px; padding-left: 16px;">
                                @foreach($upcomingAppointments as $apt)
                                    <li>{{ $apt->scheduled_for->format('M d, Y h:i A') }} - {{ $apt->scheduled_end?->format('h:i A') }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>
            </div>
            <div class="card">
                <h2 style="margin-bottom:24px;">Appointment details</h2>
                <form method="POST" action="{{ route('appointments.store') }}">
                    @csrf
                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                    <input type="hidden" name="department_id" value="{{ $doctor->department_id }}">
                    @if(isset($followUpParentId))
                        <input type="hidden" name="follow_up_for" value="{{ $followUpParentId }}">
                    @endif

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
                        <input type="text" name="patient_phone" value="{{ old('patient_phone', auth()->user()?->phone) }}" required>
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
                        <input type="datetime-local" name="scheduled_for" value="{{ old('scheduled_for', isset($followUpDate) ? $followUpDate.'T09:00' : '') }}" required>
                    </label>

                    @if(isset($followUpFee))
                        <div class="notice" style="margin-bottom: 16px; background-color: var(--surface-raised); padding: 12px; border-radius: 8px; border: 1px solid var(--border);">
                            <strong>Special Follow-up Fee:</strong> BDT {{ number_format($followUpFee, 2) }}
                            <p class="muted" style="margin-top: 4px; font-size: 13px;">This discounted fee is applied because you are booking a follow-up appointment as requested by your doctor.</p>
                        </div>
                    @endif

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
