@extends('layouts.app')
@section('title', 'Edit Patient Profile')

@section('content')
<section class="section">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <h1>Edit Profile: {{ $patient->name }}</h1>
        <a class="ghost-button" href="{{ route('admin.appointments.index') }}">Back to Appointments</a>
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

    <div class="card" style="max-width: 800px;">
        <form method="POST" action="{{ route('admin.patients.profile.update', $patient) }}">
            @csrf
            @method('PATCH')
            
            <h2 style="font-size: 1.5rem; margin-bottom: 16px;">Medical Details</h2>
            
            <div class="grid cols-2" style="margin-bottom: 24px;">
                <div>
                    <label>Date of birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                </div>
                <div>
                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select gender</option>
                        <option value="Male" @selected(old('gender', $profile?->gender) === 'Male')>Male</option>
                        <option value="Female" @selected(old('gender', $profile?->gender) === 'Female')>Female</option>
                        <option value="Other" @selected(old('gender', $profile?->gender) === 'Other')>Other</option>
                    </select>
                </div>
                <div>
                    <label>Height (e.g. 175 cm)</label>
                    <input type="text" name="height" value="{{ old('height', $profile?->height) }}" placeholder="175 cm">
                </div>
                <div>
                    <label>Weight (e.g. 70 kg)</label>
                    <input type="text" name="weight" value="{{ old('weight', $profile?->weight) }}" placeholder="70 kg">
                </div>
                <div style="grid-column: span 2;">
                    <label>Known allergies</label>
                    <input type="text" name="allergies" value="{{ old('allergies', $profile?->allergies) }}" placeholder="penicillin, ibuprofen">
                </div>
                <div style="grid-column: span 2;">
                    <label>Known conditions</label>
                    <textarea name="known_conditions" rows="2">{{ old('known_conditions', $profile?->known_conditions) }}</textarea>
                </div>
                <div style="grid-column: span 2;">
                    <label>Medical notes</label>
                    <textarea name="medical_notes" rows="2">{{ old('medical_notes', $profile?->medical_notes) }}</textarea>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="button">Update Profile</button>
            </div>
        </form>
    </div>
</section>
@endsection
