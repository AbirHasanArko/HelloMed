@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="grid cols-2">
            <div class="card">
                <div class="tag">Appointment details</div>
                <h1>{{ $appointment->doctor?->name }}</h1>
                <p>{{ $appointment->department?->name }} · {{ ucfirst($appointment->service_mode) }}</p>
                <p><strong>Scheduled for:</strong> {{ $appointment->scheduled_for?->format('M d, Y h:i A') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>
                <p><strong>Payment:</strong> {{ str_replace('_', ' ', ucfirst($appointment->payment_status)) }}</p>
                <p><strong>Reason:</strong> {{ $appointment->reason }}</p>

                @if ($appointment->service_mode === 'online')
                    <p>
                        <strong>Meeting link:</strong>
                        @if ($appointment->online_meeting_link)
                            <a href="{{ $appointment->online_meeting_link }}" target="_blank">Join online consultation</a>
                        @else
                            <span class="muted">Doctor has not added the meeting link yet.</span>
                        @endif
                    </p>
                @endif

                @if ($appointment->doctor_prescription || $appointment->prescriptionItems->isNotEmpty())
                    <div style="margin-top:12px;">
                        <strong>Doctor prescription:</strong>
                        @if ($appointment->prescription_diagnosis)
                            <p><strong>Diagnosis:</strong></p>
                            <p style="white-space:pre-wrap;">{{ $appointment->prescription_diagnosis }}</p>
                        @endif
                        @if ($appointment->prescription_medicines)
                            <p><strong>Medicines:</strong></p>
                            <p style="white-space:pre-wrap;">{{ $appointment->prescription_medicines }}</p>
                        @endif
                        @if ($appointment->prescriptionItems->isNotEmpty())
                            <p><strong>Structured medicine plan:</strong></p>
                            <div class="list">
                                @foreach ($appointment->prescriptionItems as $item)
                                    <div class="list-item">
                                        <strong>{{ $item->medicine_name }}</strong>
                                        <p>
                                            Amount: {{ $item->amount ?: 'N/A' }} ·
                                            Dosage: {{ $item->dosage ?: 'N/A' }} ·
                                            Time: {{ $item->intake_time ?: 'N/A' }}
                                        </p>
                                        @if ($item->instructions)
                                            <p class="muted">Note: {{ $item->instructions }}</p>
                                        @endif
                                        @if ($item->medicine_id && $item->medicine)
                                            <a class="ghost-button" href="{{ route('medicines.show', $item->medicine) }}">Buy from hospital medicine shop</a>
                                        @else
                                            <a class="ghost-button" href="{{ route('medicines.index') }}">Open hospital medicine shop</a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if ($appointment->prescription_advice)
                            <p><strong>Advice:</strong></p>
                            <p style="white-space:pre-wrap;">{{ $appointment->prescription_advice }}</p>
                        @endif
                        @if ($appointment->prescription_safety_notes)
                            <p><strong>Safety notes:</strong></p>
                            <p style="white-space:pre-wrap; color:#9f1239;">{{ $appointment->prescription_safety_notes }}</p>
                        @endif
                        @if ($appointment->prescription_follow_up_date)
                            <p><strong>Follow-up date:</strong> {{ $appointment->prescription_follow_up_date?->format('M d, Y') }}</p>
                        @endif
                        @if ($appointment->prescriptionItems->isNotEmpty())
                            <a class="ghost-button" href="{{ route('patient.appointments.buy-all-medicines', $appointment) }}">Buy all prescribed medicines</a>
                        @endif
                        <a class="ghost-button" href="{{ route('patient.appointments.prescription-pdf', $appointment) }}">Download prescription PDF</a>
                    </div>
                @endif
            </div>

            <div class="card">
                <h3>Manage appointment</h3>
                <form method="POST" action="{{ route('patient.appointments.update', $appointment) }}" style="margin-bottom: 20px;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="reschedule">
                    <label>
                        New schedule
                        <input type="datetime-local" name="scheduled_for" required>
                    </label>
                    <button class="button" type="submit">Reschedule</button>
                </form>

                <form method="POST" action="{{ route('patient.appointments.update', $appointment) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="cancel">
                    <button class="ghost-button" type="submit">Cancel appointment</button>
                </form>
            </div>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>Payment timeline</h3>
            <div class="list">
                @forelse ($appointment->payments as $payment)
                    <div class="list-item">
                        <strong>{{ strtoupper($payment->method) }} · BDT {{ number_format((float) $payment->amount, 2) }}</strong>
                        <p>Status: {{ ucfirst($payment->status) }} {{ $payment->paid_at ? 'on '.$payment->paid_at->format('M d, Y h:i A') : '' }}</p>
                    </div>
                @empty
                    <div class="list-item">No payment records for this appointment.</div>
                @endforelse
            </div>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>Chat with doctor</h3>
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
                                listEl.innerHTML = '<div class="list-item">No messages yet. Start the conversation.</div>';
                                return;
                            }

                            listEl.innerHTML = messages.map((msg) => {
                                const who = msg.is_mine ? 'You' : 'Doctor';
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
