@extends('layouts.app')

@section('content')
<section class="section">
    <div class="card">
        <div class="tag">Doctor consultation panel</div>
        <h1>{{ $appointment->patient_name }}</h1>
        <p><strong>Mode:</strong> {{ ucfirst($appointment->service_mode) }}</p>
        <p><strong>When:</strong> {{ $appointment->scheduled_for?->format('M d, Y h:i A') }}</p>
        <p><strong>Patient email:</strong> {{ $appointment->patient_email }}</p>
        <p><strong>Patient phone:</strong> {{ $appointment->patient_phone }}</p>
        <p><strong>Reason:</strong> {{ $appointment->reason }}</p>
        <p><strong>Status:</strong> <span class="tag" style="margin: 0; padding: 2px 8px;">{{ ucfirst($appointment->status) }}</span></p>

        @if(in_array($appointment->status, ['pending', 'confirmed']))
            <form method="POST" action="{{ route('doctor.appointments.complete', $appointment) }}" style="margin-top: 16px;">
                @csrf
                @method('PATCH')
                <button type="submit" class="button" style="padding: 6px 14px; font-size: 13px;">Mark Appointment as Complete</button>
            </form>
        @endif
    </div>

    @if ($appointment->service_mode === 'online')
        <div class="card" style="margin-top:20px;">
            <h3>Online meeting link</h3>
            @if ($appointment->online_meeting_link)
                <p><a href="{{ $appointment->online_meeting_link }}" target="_blank">Current meeting link</a></p>
            @endif
            <form method="POST" action="{{ route('doctor.appointments.meeting-link.update', $appointment) }}">
                @csrf
                @method('PATCH')
                <label>
                    Meeting URL
                    <input type="url" name="online_meeting_link" value="{{ old('online_meeting_link', $appointment->online_meeting_link) }}" required>
                </label>
                <button class="button" type="submit">Save meeting link</button>
            </form>
        </div>
    @endif

    <div class="card" style="margin-top:20px;">
        <h3>Write professional prescription</h3>
        @if ($appointment->user)
            @php $p = $appointment->user->patientProfile; @endphp
            <div class="notice" style="margin-bottom: 16px;">
                <strong>Patient Medical Profile:</strong>
                <form method="POST" action="{{ route('doctor.appointments.patient-profile.update', $appointment) }}" style="margin-top: 8px;">
                    @csrf
                    @method('PATCH')
                    <div class="grid cols-2" style="font-size: 14px; align-items: end; gap: 8px;">
                        <label>DOB <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $p?->date_of_birth?->format('Y-m-d')) }}"></label>
                        <label>Gender <select name="gender">
                            <option value="">--</option>
                            <option value="Male" @selected(old('gender', $p?->gender) === 'Male')>Male</option>
                            <option value="Female" @selected(old('gender', $p?->gender) === 'Female')>Female</option>
                            <option value="Other" @selected(old('gender', $p?->gender) === 'Other')>Other</option>
                        </select></label>
                        <label>Height <input type="text" name="height" value="{{ old('height', $p?->height) }}"></label>
                        <label>Weight <input type="text" name="weight" value="{{ old('weight', $p?->weight) }}"></label>
                        <label style="grid-column: span 2;">Conditions <input type="text" name="known_conditions" value="{{ old('known_conditions', $p?->known_conditions) }}"></label>
                        <label style="grid-column: span 2; color:var(--error-text);">Allergies <input type="text" name="allergies" value="{{ old('allergies', $p?->allergies) }}"></label>
                    </div>
                    <div style="text-align: right; margin-top: 8px;">
                        <button class="button" type="submit" style="padding: 4px 10px; font-size: 12px;">Update Patient Profile</button>
                    </div>
                </form>
            </div>
        @endif
        @if ($appointment->prescription_safety_notes)
            <p style="white-space: pre-wrap; color:#9f1239;"><strong>Previous safety notes:</strong> {{ $appointment->prescription_safety_notes }}</p>
        @endif
        <form method="POST" action="{{ route('doctor.appointments.prescription.update', $appointment) }}">
            @csrf
            @method('PATCH')
            <label>
                Clinical diagnosis
                <textarea name="prescription_diagnosis" required>{{ old('prescription_diagnosis', $appointment->prescription_diagnosis) }}</textarea>
            </label>

            <div style="margin-bottom: 14px;">
                <strong>Medicine plan</strong>
                <p class="muted" style="margin-top: 6px;">Add medicine name, amount, dosage, and intake time. Patients will see buy links when medicine is mapped.</p>
                <div id="prescription-items"></div>
                <button id="add-prescription-item" class="ghost-button" type="button" style="margin-top: 8px;">Add medicine line</button>
            </div>

            <label>
                Extra medicine notes (optional)
                <textarea name="prescription_medicines">{{ old('prescription_medicines', $appointment->prescription_medicines) }}</textarea>
            </label>
            <label>
                Additional advice
                <textarea name="prescription_advice">{{ old('prescription_advice', $appointment->prescription_advice) }}</textarea>
            </label>
            <label>
                Follow-up date
                <input type="date" name="prescription_follow_up_date" value="{{ old('prescription_follow_up_date', optional($appointment->prescription_follow_up_date)->format('Y-m-d')) }}">
            </label>
            <button class="button" type="submit">Save prescription</button>
        </form>
    </div>

    <script>
        (() => {
            const container = document.getElementById('prescription-items');
            const addBtn = document.getElementById('add-prescription-item');

            if (!container || !addBtn) {
                return;
            }

            const medicines = @json($medicinesForJs);

            const existingItems = @json($existingPrescriptionItemsForJs);

            const medicineOptions = medicines.map((medicine) => {
                const hint = [medicine.power, medicine.amount].filter(Boolean).join(' · ');
                return `<option value="${medicine.id}">${medicine.name}${hint ? ` (${hint})` : ''}</option>`;
            }).join('');

            const medicineLookupOptions = medicines.map((medicine) => {
                const hint = [medicine.power, medicine.amount].filter(Boolean).join(' · ');
                const label = `${medicine.name}${hint ? ` (${hint})` : ''}`;
                return `<option value="${label}" data-id="${medicine.id}" data-name="${medicine.name}" data-amount="${medicine.amount || ''}"></option>`;
            }).join('');

            const lookupListId = 'medicine-lookup-list';
            const lookupList = document.createElement('datalist');
            lookupList.id = lookupListId;
            lookupList.innerHTML = medicineLookupOptions;
            document.body.appendChild(lookupList);

            let index = 0;

            const buildItemRow = (item = {}) => {
                const row = document.createElement('div');
                row.className = 'card';
                row.style.marginTop = '10px';
                row.style.padding = '14px';
                const currentIndex = index++;

                row.innerHTML = `
                    <div class="grid cols-2">
                        <label>
                            Search medicine (typeahead)
                            <input type="text" name="prescription_items[${currentIndex}][medicine_lookup]" list="${lookupListId}" placeholder="Type medicine name">
                        </label>
                        <label>
                            Select medicine from shop (optional)
                            <select name="prescription_items[${currentIndex}][medicine_id]">
                                <option value="">Custom / not in list</option>
                                ${medicineOptions}
                            </select>
                        </label>
                        <label>
                            Medicine name
                            <input type="text" name="prescription_items[${currentIndex}][medicine_name]">
                        </label>
                        <label>
                            Amount
                            <input type="text" name="prescription_items[${currentIndex}][amount]" placeholder="1 tablet / 5ml / 1 strip">
                        </label>
                        <label>
                            Dosage
                            <input type="text" name="prescription_items[${currentIndex}][dosage]" placeholder="1+0+1 for 5 days">
                        </label>
                        <label>
                            Intake time
                            <input type="text" name="prescription_items[${currentIndex}][intake_time]" placeholder="After meal / Before sleep">
                        </label>
                        <label>
                            Instructions
                            <input type="text" name="prescription_items[${currentIndex}][instructions]" placeholder="With water">
                        </label>
                    </div>
                    <button class="ghost-button remove-item" type="button">Remove</button>
                `;

                const selectEl = row.querySelector(`select[name="prescription_items[${currentIndex}][medicine_id]"]`);
                const lookupEl = row.querySelector(`input[name="prescription_items[${currentIndex}][medicine_lookup]"]`);
                const nameEl = row.querySelector(`input[name="prescription_items[${currentIndex}][medicine_name]"]`);
                const amountEl = row.querySelector(`input[name="prescription_items[${currentIndex}][amount]"]`);
                const dosageEl = row.querySelector(`input[name="prescription_items[${currentIndex}][dosage]"]`);
                const intakeEl = row.querySelector(`input[name="prescription_items[${currentIndex}][intake_time]"]`);
                const notesEl = row.querySelector(`input[name="prescription_items[${currentIndex}][instructions]"]`);

                if (item.medicine_id) {
                    selectEl.value = String(item.medicine_id);
                }
                nameEl.value = item.medicine_name || '';
                amountEl.value = item.amount || '';
                dosageEl.value = item.dosage || '';
                intakeEl.value = item.intake_time || '';
                notesEl.value = item.instructions || '';

                if (item.medicine_name) {
                    lookupEl.value = item.medicine_name;
                }

                selectEl.addEventListener('change', () => {
                    const selected = medicines.find((medicine) => String(medicine.id) === String(selectEl.value));
                    if (!selected) {
                        return;
                    }
                    if (!nameEl.value.trim()) {
                        nameEl.value = selected.name;
                    }
                    if (!amountEl.value.trim() && selected.amount) {
                        amountEl.value = selected.amount;
                    }
                    lookupEl.value = selected.name;
                });

                lookupEl.addEventListener('change', () => {
                    const optionEl = Array.from(lookupList.options).find((option) => option.value === lookupEl.value);
                    if (!optionEl) {
                        return;
                    }

                    const medicineId = optionEl.getAttribute('data-id');
                    const medicineName = optionEl.getAttribute('data-name');
                    const medicineAmount = optionEl.getAttribute('data-amount');

                    if (medicineId) {
                        selectEl.value = medicineId;
                    }
                    if (medicineName) {
                        nameEl.value = medicineName;
                    }
                    if (!amountEl.value.trim() && medicineAmount) {
                        amountEl.value = medicineAmount;
                    }
                });

                row.querySelector('.remove-item').addEventListener('click', () => {
                    row.remove();
                });

                return row;
            };

            addBtn.addEventListener('click', () => {
                container.appendChild(buildItemRow());
            });

            if (existingItems.length) {
                existingItems.forEach((item) => container.appendChild(buildItemRow(item)));
            } else {
                container.appendChild(buildItemRow());
            }
        })();
    </script>

    <div class="card" style="margin-top:20px;">
        <h3>Diagnostics Service Requests</h3>
        <p class="muted" style="margin-bottom: 12px;">Request diagnostics services for the patient. Staff will process these and upload results.</p>
        
        @if($appointment->labTests && $appointment->labTests->isNotEmpty())
            <div style="margin-bottom: 16px; border: 1px solid var(--border-color); border-radius: 4px; padding: 8px;">
                <table style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 8px;">Test Name</th>
                            <th style="padding: 8px;">Notes</th>
                            <th style="padding: 8px;">Status</th>
                            <th style="padding: 8px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointment->labTests as $test)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 8px;">{{ $test->test_name }}</td>
                            <td style="padding: 8px;">{{ $test->notes ?: '-' }}</td>
                            <td style="padding: 8px;">
                                @if($test->status === 'completed')
                                    <span style="color: green; font-weight: bold;">Completed</span>
                                @else
                                    <span style="color: orange; font-weight: bold;">Pending</span>
                                @endif
                            </td>
                            <td style="padding: 8px;">
                                @if($test->status === 'completed')
                                    <a href="{{ route('diagnostic-services.download', $test) }}" target="_blank" class="button" style="padding: 4px 8px; font-size: 12px;">Download Result</a>
                                @else
                                    <form method="POST" action="{{ route('doctor.diagnostic-services.destroy', $test) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ghost-button" style="color: var(--error-text); padding: 4px 8px; font-size: 12px;" onclick="return confirm('Delete this diagnostics service request?')">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <form id="lab-test-form" method="POST" action="{{ route('doctor.appointments.diagnostic-services.store', $appointment) }}">
            @csrf
            <div id="lab-test-items"></div>
            <div style="display: flex; gap: 12px; margin-top: 12px;">
                <button type="button" id="add-lab-test-btn" class="ghost-button">Add diagnostics service</button>
                <button class="button" type="submit">Submit Requests</button>
            </div>
        </form>

        <datalist id="available-tests-list">
            @foreach($availableTests as $test)
                <option value="{{ $test->name }}">{{ $test->name }} (BDT {{ $test->fee_bdt }})</option>
            @endforeach
        </datalist>

        <script>
            (() => {
                const container = document.getElementById('lab-test-items');
                const addBtn = document.getElementById('add-lab-test-btn');
                let index = 0;

                const buildRow = () => {
                    const row = document.createElement('div');
                    row.className = 'grid cols-2';
                    row.style.gap = '16px';
                    row.style.alignItems = 'end';
                    row.style.marginBottom = '12px';
                    row.style.padding = '12px';
                    row.style.border = '1px solid var(--border-color)';
                    row.style.borderRadius = '4px';
                    row.style.background = '#f8fafc';
                    
                    row.innerHTML = `
                        <label style="margin-bottom: 0;">
                            Test Name
                            <input type="text" name="test_names[]" list="available-tests-list" placeholder="Type to search available tests..." required>
                        </label>
                        <div style="display: flex; gap: 16px; align-items: flex-end;">
                            <label style="flex: 1; margin-bottom: 0;">
                                Notes (Optional)
                                <input type="text" name="notes[]" placeholder="Instructions for staff/patient">
                            </label>
                            <button class="ghost-button remove-test" type="button" style="color: var(--error-text); margin-bottom: 4px;">Remove</button>
                        </div>
                    `;

                    row.querySelector('.remove-test').addEventListener('click', () => {
                        row.remove();
                    });
                    return row;
                };

                addBtn.addEventListener('click', () => container.appendChild(buildRow()));
                
                // Add one row initially to encourage input, but they can remove it
                container.appendChild(buildRow());

                document.getElementById('lab-test-form').addEventListener('submit', (e) => {
                    // Filter out empty rows before submitting if they just pressed submit on a half-filled form
                    const inputs = container.querySelectorAll('input[name="test_names[]"]');
                    let hasValue = false;
                    inputs.forEach(input => {
                        if (input.value.trim() !== '') hasValue = true;
                    });
                    
                    if (!hasValue && container.children.length > 0) {
                        e.preventDefault();
                        alert('Please fill out the test name or remove the empty row.');
                    }
                });
            })();
        </script>
    </div>

    <div class="card" style="margin-top:20px;">
        <h3>Chat with patient</h3>
        @if ($appointment->status !== 'confirmed')
            <p class="muted">Chat becomes available after appointment is confirmed.</p>
        @else
            <div id="chat-messages" class="list" style="margin-bottom: 16px; max-height: 360px; overflow: auto;"></div>

            <form id="chat-form" method="POST" action="{{ route('appointments.chat.store', $appointment) }}" enctype="multipart/form-data">
                @csrf
                <label>
                    Message
                    <textarea name="message"></textarea>
                </label>
                <label>
                    Attachment (optional)
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                </label>
                <button class="button" type="submit">Send message</button>
            </form>

            <script>
                (() => {
                    const listEl = document.getElementById('chat-messages');
                    const formEl = document.getElementById('chat-form');
                    const messagesUrl = "{{ route('appointments.chat.messages', $appointment) }}";
                    const storeUrl = "{{ route('appointments.chat.store', $appointment) }}";
                    const readUrl = "{{ route('appointments.chat.read', $appointment) }}";

                    const escapeHtml = (value) => {
                        const div = document.createElement('div');
                        div.textContent = value ?? '';
                        return div.innerHTML;
                    };

                    const render = (messages) => {
                        if (!messages.length) {
                            listEl.innerHTML = '<div class="list-item">No messages yet.</div>';
                            return;
                        }

                        listEl.innerHTML = messages.map((msg) => {
                            const who = msg.is_mine ? 'You' : 'Patient';
                            const status = msg.is_mine ? (msg.read_at ? 'Read' : 'Unread') : 'Received';
                            const attachment = msg.attachment_url
                                ? `<p><a href="${msg.attachment_url}" target="_blank">${escapeHtml(msg.attachment_name || 'Attachment')}</a></p>`
                                : '';

                            return `<div class="list-item">
                                <strong>${escapeHtml(msg.sender_name || 'User')} (${who})</strong>
                                ${msg.message ? `<p style="white-space: pre-wrap;">${escapeHtml(msg.message)}</p>` : ''}
                                ${attachment}
                                <p class="muted">${escapeHtml(msg.created_at || '')} · ${status}</p>
                            </div>`;
                        }).join('');

                        listEl.scrollTop = listEl.scrollHeight;
                    };

                    const syncMessages = async () => {
                        const res = await fetch(messagesUrl, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) return;
                        const data = await res.json();
                        if (!data.enabled) return;
                        render(data.messages || []);

                        await fetch(readUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': formEl.querySelector('input[name="_token"]').value,
                            },
                        });
                    };

                    formEl.addEventListener('submit', async (event) => {
                        event.preventDefault();

                        const formData = new FormData(formEl);
                        const message = (formData.get('message') || '').toString().trim();
                        const attachment = formData.get('attachment');
                        const hasFile = attachment && attachment instanceof File && attachment.size > 0;
                        if (!message && !hasFile) {
                            return;
                        }

                        const res = await fetch(storeUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                            },
                        });

                        if (res.ok || res.redirected) {
                            formEl.reset();
                            await syncMessages();
                        }
                    });

                    syncMessages();
                    setInterval(syncMessages, 5000);
                })();
            </script>
        @endif
    </div>
</section>
@endsection
