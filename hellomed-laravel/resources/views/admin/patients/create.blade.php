@extends('layouts.app')

@section('content')
<section class="section">
    <div style="margin-bottom: 24px;">
        <h1 style="margin-bottom: 4px;">Add New Patient</h1>
        <p class="muted">Create a new patient account and profile.</p>
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
        <form method="POST" action="{{ route($routePrefix . '.patients.store') }}">
            @csrf
            
            <h3 style="margin-bottom: 16px;">Account Information</h3>
            <div class="grid cols-2">
                <label>
                    Full Name
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </label>
                <label>
                    Email Address
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </label>
            </div>
            <div class="grid cols-2">
                <label>
                    Phone Number
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </label>
                <label>
                    Password
                    <input type="password" name="password" required>
                </label>
            </div>

            <hr style="margin:24px 0; border:0; border-top:1px solid var(--border);">
            
            <h3 style="margin-bottom: 16px;">Medical Profile</h3>
            <div class="grid cols-2">
                <label>
                    Date of Birth
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}">
                </label>
                <label>
                    Gender
                    <select name="gender">
                        <option value="">Select...</option>
                        <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                        <option value="Female" @selected(old('gender') === 'Female')>Female</option>
                        <option value="Other" @selected(old('gender') === 'Other')>Other</option>
                    </select>
                </label>
                <label>
                    Height
                    <input type="text" name="height" value="{{ old('height') }}" placeholder="e.g. 5 ft 10 in">
                </label>
                <label>
                    Weight
                    <input type="text" name="weight" value="{{ old('weight') }}" placeholder="e.g. 70 kg">
                </label>
            </div>

            <label>
                Allergies
                <textarea name="allergies">{{ old('allergies') }}</textarea>
            </label>
            <label>
                Known Conditions
                <textarea name="known_conditions">{{ old('known_conditions') }}</textarea>
            </label>
            <label>
                Medical Notes
                <textarea name="medical_notes">{{ old('medical_notes') }}</textarea>
            </label>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button class="button" type="submit">Create Patient</button>
                <a class="ghost-button" href="{{ route($routePrefix . '.patients.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
