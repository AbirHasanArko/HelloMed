@extends('layouts.app')

@section('content')
<section class="section">
    <div style="margin-bottom: 24px;">
        <h1 style="margin-bottom: 4px;">Edit Patient</h1>
        <p class="muted">Update account and profile details for {{ $patient->name }}.</p>
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

    <div class="card" style="max-width: 800px;">
        <form method="POST" action="{{ route($routePrefix . '.patients.update', $patient) }}">
            @csrf
            @method('PUT')
            
            <h3 style="margin-bottom: 16px;">Account Information</h3>
            <div class="grid cols-2">
                <label>
                    Full Name
                    <input type="text" name="name" value="{{ old('name', $patient->name) }}" required>
                </label>
                <label>
                    Email Address
                    <input type="email" name="email" value="{{ old('email', $patient->email) }}" required>
                </label>
            </div>
            <div class="grid cols-2">
                <label>
                    Phone Number
                    <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}">
                </label>
                <label>
                    New Password <span class="muted">(leave blank to keep current)</span>
                    <input type="password" name="password">
                </label>
            </div>

            <hr style="margin:24px 0; border:0; border-top:1px solid var(--border);">
            
            <h3 style="margin-bottom: 16px;">Medical Profile</h3>
            <div class="grid cols-2">
                <label>
                    Date of Birth
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile?->date_of_birth) }}">
                </label>
                <label>
                    Gender
                    <select name="gender">
                        <option value="">Select...</option>
                        <option value="Male" @selected(old('gender', $profile?->gender) === 'Male')>Male</option>
                        <option value="Female" @selected(old('gender', $profile?->gender) === 'Female')>Female</option>
                        <option value="Other" @selected(old('gender', $profile?->gender) === 'Other')>Other</option>
                    </select>
                </label>
                <label>
                    Height
                    <input type="text" name="height" value="{{ old('height', $profile?->height) }}" placeholder="e.g. 5 ft 10 in">
                </label>
                <label>
                    Weight
                    <input type="text" name="weight" value="{{ old('weight', $profile?->weight) }}" placeholder="e.g. 70 kg">
                </label>
            </div>

            <label>
                Allergies
                <textarea name="allergies">{{ old('allergies', $profile?->allergies) }}</textarea>
            </label>
            <label>
                Known Conditions
                <textarea name="known_conditions">{{ old('known_conditions', $profile?->known_conditions) }}</textarea>
            </label>
            <label>
                Medical Notes
                <textarea name="medical_notes">{{ old('medical_notes', $profile?->medical_notes) }}</textarea>
            </label>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button class="button" type="submit">Update Patient</button>
                <a class="ghost-button" href="{{ route($routePrefix . '.patients.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
