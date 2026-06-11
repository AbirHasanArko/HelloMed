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
            <p class="muted">Search existing patient or fill in details. If no match and email is provided, an account is created.</p>
            
            <div style="position: relative; margin-bottom: 24px;" id="patient-search-wrapper">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Search Existing Patient (Phone or Email)</label>
                <input type="text" id="patient_search" class="input" placeholder="Type phone number or email..." autocomplete="off" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px;">
                <div class="autocomplete-dropdown" id="patient-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--surface-raised); border: 1px solid var(--border); border-radius: 8px; margin-top: 4px; box-shadow: var(--shadow-lg); z-index: 50; max-height: 250px; overflow-y: auto;">
                    <div class="autocomplete-results"></div>
                </div>
            </div>

            <div class="grid cols-2" style="margin-bottom: 24px;">
                <div>
                    <label>Patient Full Name <span style="color:var(--error-text);">*</span></label>
                    <input type="text" name="patient_name" id="patient_name" value="{{ old('patient_name') }}" required>
                </div>
                <div>
                    <label>Patient Email (Optional)</label>
                    <input type="email" name="patient_email" id="patient_email" value="{{ old('patient_email') }}">
                </div>
                <div>
                    <label>Patient Phone Number <span style="color:var(--error-text);">*</span></label>
                    <input type="tel" name="patient_phone" id="patient_phone" value="{{ old('patient_phone') }}" required>
                </div>
            </div>

            <hr style="border:0; border-top:1px solid var(--border); margin: 32px 0;">

            <h2 style="font-size: 1.5rem; margin-bottom: 16px;">2. Patient Medical Profile (Optional)</h2>
            <p class="muted">Staff can enter medical details for the patient.</p>
            <div class="grid cols-2" style="margin-bottom: 24px;">
                <div>
                    <label>Date of birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d') }}">
                </div>
                <div>
                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select gender</option>
                        <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                        <option value="Female" @selected(old('gender') === 'Female')>Female</option>
                        <option value="Other" @selected(old('gender') === 'Other')>Other</option>
                    </select>
                </div>
                <div>
                    <label>Height (e.g. 175 cm)</label>
                    <input type="text" name="height" value="{{ old('height') }}" placeholder="175 cm">
                </div>
                <div>
                    <label>Weight (e.g. 70 kg)</label>
                    <input type="text" name="weight" value="{{ old('weight') }}" placeholder="70 kg">
                </div>
                <div style="grid-column: span 2;">
                    <label>Known allergies</label>
                    <input type="text" name="allergies" value="{{ old('allergies') }}" placeholder="penicillin, ibuprofen">
                </div>
                <div style="grid-column: span 2;">
                    <label>Known conditions</label>
                    <textarea name="known_conditions" rows="2">{{ old('known_conditions') }}</textarea>
                </div>
                <div style="grid-column: span 2;">
                    <label>Medical notes</label>
                    <textarea name="medical_notes" rows="2">{{ old('medical_notes') }}</textarea>
                </div>
            </div>

            <hr style="border:0; border-top:1px solid var(--border); margin: 32px 0;">

            <h2 style="font-size: 1.5rem; margin-bottom: 16px;">3. Appointment Details</h2>
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
                    <label>Reason for Visit <span style="color:var(--error-text);">*</span></label>
                    <textarea name="reason" rows="3" required>{{ old('reason') }}</textarea>
                </div>
                <div style="grid-column: span 2; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border);">
                    <label>Override Payment Amount (Optional)</label>
                    <p class="muted" style="margin-top: 0; margin-bottom: 8px;">If provided, a payment record will be created for this amount as paid at the cash counter.</p>
                    <input type="number" step="0.01" min="0" name="override_payment_amount" value="{{ old('override_payment_amount') }}" placeholder="e.g. 500">
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

<style>
    .autocomplete-item:hover { background: var(--surface-hover); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Patient Search
    const patientInput = document.getElementById('patient_search');
    const patientDropdown = document.getElementById('patient-dropdown');
    const patientResults = patientDropdown.querySelector('.autocomplete-results');
    let patientTimer;

    patientInput.addEventListener('input', function() {
        clearTimeout(patientTimer);
        const query = this.value.trim();
        
        if (query.length < 2) {
            patientDropdown.style.display = 'none';
            return;
        }

        patientTimer = setTimeout(() => {
            fetch(`{{ route('staff.api.patients') }}?query=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                patientResults.innerHTML = '';
                if (data.length === 0) {
                    patientResults.innerHTML = '<div style="padding: 12px 16px; color: var(--muted);">No matching patients found.</div>';
                } else {
                    data.forEach(user => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.style.cssText = 'padding: 12px 16px; cursor: pointer; border-bottom: 1px solid var(--border-light);';
                        div.innerHTML = `<strong>${user.name}</strong> <div style="font-size: 12px; color: var(--muted); margin-top:4px;">${user.phone} | ${user.email}</div>`;
                        
                        div.addEventListener('click', () => {
                            document.getElementById('patient_name').value = user.name || '';
                            document.getElementById('patient_phone').value = user.phone || '';
                            document.getElementById('patient_email').value = user.email || '';
                            
                            // Auto fill medical profile if exists
                            if (user.patient_profile) {
                                const p = user.patient_profile;
                                if (document.querySelector('input[name="date_of_birth"]') && p.date_of_birth) 
                                    document.querySelector('input[name="date_of_birth"]').value = p.date_of_birth.split('T')[0];
                                if (document.querySelector('select[name="gender"]') && p.gender) 
                                    document.querySelector('select[name="gender"]').value = p.gender;
                                if (document.querySelector('input[name="height"]')) 
                                    document.querySelector('input[name="height"]').value = p.height || '';
                                if (document.querySelector('input[name="weight"]')) 
                                    document.querySelector('input[name="weight"]').value = p.weight || '';
                                if (document.querySelector('input[name="allergies"]')) 
                                    document.querySelector('input[name="allergies"]').value = p.allergies || '';
                                if (document.querySelector('textarea[name="known_conditions"]')) 
                                    document.querySelector('textarea[name="known_conditions"]').value = p.known_conditions || '';
                                if (document.querySelector('textarea[name="medical_notes"]')) 
                                    document.querySelector('textarea[name="medical_notes"]').value = p.medical_notes || '';
                            }

                            patientInput.value = '';
                            patientDropdown.style.display = 'none';
                        });
                        patientResults.appendChild(div);
                    });
                }
                patientDropdown.style.display = 'block';
            });
        }, 300);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!document.getElementById('patient-search-wrapper').contains(e.target)) {
            patientDropdown.style.display = 'none';
        }
    });

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
