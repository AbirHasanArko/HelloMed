@extends('layouts.app')
@section('title', 'Book Offline Appointment')

@section('content')
<section class="section">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <h1>➕ Book Offline Appointment</h1>
        <a class="ghost-button" href="{{ route('staff.dashboard') }}">Back to Dashboard</a>
    </div>

    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card fade-in" style="max-width: 800px;">
        <form method="POST" action="{{ route('staff.offline-appointments.store') }}">
            @csrf
            
            <h2 style="font-size: 1.5rem; margin-bottom: 16px;">1. Patient Details</h2>
            <p class="muted">If the email doesn't exist, a new account will be automatically created.</p>
            <div class="grid cols-2" style="margin-bottom: 24px;">
                <div>
                    <label>Patient Full Name</label>
                    <input type="text" name="patient_name" value="{{ old('patient_name') }}" required>
                </div>
                <div>
                    <label>Patient Email</label>
                    <input type="email" name="patient_email" value="{{ old('patient_email') }}" required>
                </div>
                <div>
                    <label>Patient Phone Number</label>
                    <input type="tel" name="patient_phone" value="{{ old('patient_phone') }}" required>
                </div>
            </div>

            <hr style="border:0; border-top:1px solid var(--border); margin: 32px 0;">

            <h2 style="font-size: 1.5rem; margin-bottom: 16px;">2. Appointment Details</h2>
            <div class="grid cols-2" style="margin-bottom: 24px;">
                <div style="grid-column: span 2;">
                    <label>Select Doctor</label>
                    <select name="doctor_id" required>
                        <option value="" disabled selected>-- Choose a Doctor --</option>
                        @foreach($departments as $department)
                            <optgroup label="{{ $department->name }}">
                                @foreach($department->doctors as $doctor)
                                    <option value="{{ $doctor->id }}" @selected(old('doctor_id') == $doctor->id)>
                                        {{ $doctor->name }} ({{ $doctor->qualifications }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Date</label>
                    <input type="date" name="scheduled_date" min="{{ date('Y-m-d') }}" value="{{ old('scheduled_date') }}" required>
                </div>
                <div>
                    <label>Time</label>
                    <input type="time" name="scheduled_time" value="{{ old('scheduled_time') }}" required>
                </div>
                <div style="grid-column: span 2;">
                    <label>Reason for Visit</label>
                    <textarea name="reason" rows="3" required>{{ old('reason') }}</textarea>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="button">Confirm Appointment</button>
            </div>
        </form>
        
        <div id="doctor-schedule-container" style="display: none; margin-top: 32px; padding: 16px; background: rgba(0,0,0,0.05); border-radius: 8px;">
            <h3 style="margin-bottom: 12px; font-size: 1.1rem;">Selected Doctor's Information</h3>
            <div id="doctor-schedule-content" style="font-size: 14px; margin-bottom: 16px;"></div>
            
            <h4 style="margin-bottom: 8px; font-size: 1rem;">Recently Booked Slots (Next 14 Days)</h4>
            <ul id="doctor-booked-slots" style="font-size: 13px; padding-left: 16px;"></ul>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.querySelector('select[name="doctor_id"]');
    const scheduleContainer = document.getElementById('doctor-schedule-container');
    const scheduleContent = document.getElementById('doctor-schedule-content');
    const bookedSlotsList = document.getElementById('doctor-booked-slots');

    doctorSelect.addEventListener('change', function() {
        const doctorId = this.value;
        if (!doctorId) {
            scheduleContainer.style.display = 'none';
            return;
        }

        fetch(`/api/doctors/${doctorId}/schedule`)
            .then(res => res.json())
            .then(data => {
                scheduleContainer.style.display = 'block';
                
                let html = `<p style="margin-bottom: 8px;"><strong>Slot Duration:</strong> ${data.slot_minutes} mins</p>`;
                if (data.offline_available) {
                    html += `<p style="margin-bottom: 8px;"><strong>Offline Days:</strong> ${data.offline_days.join(', ')} (${data.offline_from || 'Anytime'} - ${data.offline_to || 'Anytime'})</p>`;
                } else {
                    html += `<p style="margin-bottom: 8px; color: var(--danger);"><strong>Offline Unavailable:</strong> This doctor does not accept offline appointments.</p>`;
                }
                scheduleContent.innerHTML = html;

                bookedSlotsList.innerHTML = '';
                if (data.booked_slots && data.booked_slots.length > 0) {
                    data.booked_slots.forEach(slot => {
                        const li = document.createElement('li');
                        li.textContent = `${slot.start_formatted} - ${slot.end_formatted}`;
                        bookedSlotsList.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'No upcoming bookings in the next 14 days.';
                    li.classList.add('muted');
                    bookedSlotsList.appendChild(li);
                }
            })
            .catch(err => {
                console.error('Failed to fetch doctor schedule:', err);
                scheduleContainer.style.display = 'none';
            });
    });

    if (doctorSelect.value) {
        doctorSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
