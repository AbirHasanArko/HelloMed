@extends('layouts.app')
@section('title', 'Call Ambulance')

@section('content')
<section class="section">
    <div class="container" style="max-width: 600px;">
        <div class="card fade-in">
            <h1 style="color: var(--error-text);"><span style="font-size:2rem; vertical-align:middle;">🚑</span> Emergency Ambulance</h1>
            <p>Request an ambulance immediately. Our team is on standby 24/7.</p>
            
            <form method="POST" action="{{ route('ambulance.store') }}" id="ambulanceForm">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label>Patient Name</label>
                    <input type="text" name="patient_name" value="{{ auth()->user()->name ?? '' }}" required placeholder="Enter patient name">
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label>Contact Phone</label>
                    <input type="tel" name="patient_phone" required placeholder="Enter active phone number">
                </div>

                <div style="margin-bottom: 24px;">
                    <label>Address / Location details</label>
                    <textarea name="address" rows="3" placeholder="Enter building, street, or landmark details"></textarea>
                    
                    <div style="margin-top: 12px; display:flex; align-items:center; gap: 10px;">
                        <button type="button" class="ghost-button" id="getLocationBtn" style="color: var(--primary);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Use my current GPS Location
                        </button>
                        <span id="locationStatus" class="muted" style="font-size:13px;"></span>
                    </div>
                </div>

                <input type="hidden" name="latitude" id="latField">
                <input type="hidden" name="longitude" id="lngField">

                <button type="submit" class="button" style="width: 100%; justify-content:center; background: linear-gradient(135deg, #ef4444, #b91c1c);">
                    CALL AMBULANCE NOW
                </button>
            </form>
        </div>
    </div>
</section>

<script>
    document.getElementById('getLocationBtn').addEventListener('click', function() {
        const status = document.getElementById('locationStatus');
        
        if (!navigator.geolocation) {
            status.textContent = 'Geolocation is not supported by your browser';
            return;
        }

        status.textContent = 'Locating...';
        this.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('latField').value = position.coords.latitude;
                document.getElementById('lngField').value = position.coords.longitude;
                status.textContent = 'Location acquired! ✓';
                status.style.color = 'var(--primary)';
                document.getElementById('getLocationBtn').disabled = false;
            },
            () => {
                status.textContent = 'Unable to retrieve your location. Please type address.';
                status.style.color = 'var(--error-text)';
                document.getElementById('getLocationBtn').disabled = false;
            }
        );
    });
</script>
@endsection
