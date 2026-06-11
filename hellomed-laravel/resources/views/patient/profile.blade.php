@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<section class="section">
    <h1>My medical profile</h1>
    <p>Keep your health metrics and known conditions updated for better care.</p>

    <div class="card" style="max-width: 800px; margin-top: 24px;">
        <h3>Safety profile</h3>
        <form method="POST" action="{{ route('patient.profile.update') }}">
            @csrf
            @method('PATCH')
            <div class="grid cols-2">
                <label>
                    Date of birth
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                </label>
                <label>
                    Gender
                    <select name="gender">
                        <option value="">Select gender</option>
                        <option value="Male" @selected(old('gender', $profile?->gender) === 'Male')>Male</option>
                        <option value="Female" @selected(old('gender', $profile?->gender) === 'Female')>Female</option>
                        <option value="Other" @selected(old('gender', $profile?->gender) === 'Other')>Other</option>
                    </select>
                </label>
                <label>
                    Height (e.g. 175 cm)
                    <input type="text" name="height" value="{{ old('height', $profile?->height) }}" placeholder="175 cm">
                </label>
                <label>
                    Weight (e.g. 70 kg)
                    <input type="text" name="weight" value="{{ old('weight', $profile?->weight) }}" placeholder="70 kg">
                </label>
            </div>
            <label>
                Known allergies (comma separated)
                <input type="text" name="allergies" value="{{ old('allergies', $profile?->allergies) }}" placeholder="penicillin, ibuprofen">
            </label>
            <label>
                Known conditions
                <textarea name="known_conditions" rows="3">{{ old('known_conditions', $profile?->known_conditions) }}</textarea>
            </label>
            <label>
                Medical notes
                <textarea name="medical_notes" rows="3">{{ old('medical_notes', $profile?->medical_notes) }}</textarea>
            </label>
            <div style="margin-top: 16px; text-align: right;">
                <button class="button" type="submit">Save profile</button>
            </div>
        </form>
    </div>
</section>
@endsection
