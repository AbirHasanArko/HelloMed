@extends('layouts.app')

@section('content')
<section class="section">
    <div style="margin-bottom: 24px;">
        <a class="ghost-button" href="{{ route('available-tests.index') }}">← Back to all tests</a>
    </div>

    <div class="grid cols-2">
        <div class="card fade-in">
            <div class="tag">Lab Test Details</div>
            <h1>{{ $availableTest->name }}</h1>
            
            <div style="margin-top: 24px; padding: 20px; background: var(--surface-hover); border-radius: 12px; border: 1px solid var(--border);">
                <div style="font-size: 14px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-bottom: 8px;">Test Fee</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--primary);">BDT {{ number_format($availableTest->fee_bdt, 2) }}</div>
            </div>

            @if($availableTest->description)
                <div style="margin-top: 24px;">
                    <h3>Description</h3>
                    <p style="white-space: pre-wrap;">{{ $availableTest->description }}</p>
                </div>
            @endif
        </div>

        <div class="card fade-in fade-in-delay-1">
            <h3>Location & Instructions</h3>
            
            <div class="list" style="margin-top: 16px;">
                <div class="list-item">
                    <strong>📍 Location</strong>
                    <p>{{ $availableTest->location ?: 'Please ask the reception desk for directions.' }}</p>
                </div>
                
                @if($availableTest->lab_room_number)
                <div class="list-item">
                    <strong>🚪 Room Number</strong>
                    <p>Lab Room: {{ $availableTest->lab_room_number }}</p>
                </div>
                @endif
                
                <div class="list-item" style="background-color: var(--notice-bg); border-color: var(--notice-border);">
                    <strong style="color: var(--notice-text);">ℹ️ Note for Patients</strong>
                    <p style="color: var(--notice-text);">
                        Tests must be requested by a doctor during an appointment. Please complete the payment at the hospital counter before proceeding to the lab room.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
