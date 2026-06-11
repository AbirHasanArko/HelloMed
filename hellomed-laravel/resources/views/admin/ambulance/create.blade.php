@extends('layouts.app')

@section('content')
<section class="section">
    <div style="margin-bottom: 24px;">
        <h1 style="margin-bottom: 4px;">Create Ambulance Request</h1>
        <p class="muted">Manually dispatch or record an ambulance request.</p>
    </div>

    @if ($errors->any())
        <div class="error-box" style="margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="max-width: 600px;">
        <form method="POST" action="{{ route($routePrefix . '.ambulance.store') }}">
            @csrf
            
            <label>
                Patient Name
                <input type="text" name="patient_name" value="{{ old('patient_name') }}" required>
            </label>
            <label>
                Patient Phone
                <input type="text" name="patient_phone" value="{{ old('patient_phone') }}" required>
            </label>
            
            <hr style="margin:20px 0; border:0; border-top:1px solid var(--border);">

            <div class="grid cols-2">
                <label>
                    Latitude (Optional)
                    <input type="text" name="latitude" value="{{ old('latitude') }}">
                </label>
                <label>
                    Longitude (Optional)
                    <input type="text" name="longitude" value="{{ old('longitude') }}">
                </label>
            </div>
            
            <label>
                Full Address
                <textarea name="address" required>{{ old('address') }}</textarea>
            </label>

            <hr style="margin:20px 0; border:0; border-top:1px solid var(--border);">
            
            <label>
                Status
                <select name="status" required>
                    <option value="pending" @selected(old('status') === 'pending')>Pending</option>
                    <option value="dispatched" @selected(old('status') === 'dispatched')>Dispatched</option>
                    <option value="resolved" @selected(old('status') === 'resolved')>Resolved</option>
                    <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelled</option>
                </select>
            </label>

            <label>
                Internal Notes
                <textarea name="notes">{{ old('notes') }}</textarea>
            </label>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button class="button" type="submit">Create Request</button>
                <a class="ghost-button" href="{{ route($routePrefix . '.ambulance.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
