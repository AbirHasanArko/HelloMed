@extends('layouts.app')
@section('title', 'Account Settings')

@section('content')
<section class="section">
    <div style="margin-bottom: 24px;">
        <h1 style="margin-bottom: 4px;">Hello, {{ $user->name }}!</h1>
        <p class="muted">Manage your account settings below.</p>
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

    <div class="card" style="max-width: 600px;">
        <form method="POST" action="{{ route('settings.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            @if ($user->role === 'doctor')
                <div style="margin-bottom: 24px;">
                    <label>Profile Picture</label>
                    @if ($user->doctorProfile?->photo_path)
                        <div style="margin-bottom: 12px;">
                            <img src="{{ Storage::url($user->doctorProfile->photo_path) }}" alt="{{ $user->name }}" style="width: 120px; height: 120px; object-fit: cover; border-radius: 14px; border: 1px solid var(--border);">
                        </div>
                    @endif
                    <input type="file" name="photo" accept="image/*">
                </div>
            @endif

            <label>
                Full Name
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </label>

            <label>
                Email Address
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </label>

            <label>
                Phone Number
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
            </label>

            <label>
                Current Password <span class="muted">(required to change password)</span>
                <input type="password" name="current_password" autocomplete="current-password">
            </label>

            <label>
                New Password <span class="muted">(leave blank to keep current)</span>
                <input type="password" name="password" autocomplete="new-password">
            </label>

            <div style="margin-top: 24px;">
                <button type="submit" class="button">Save Account Settings</button>
            </div>
        </form>
    </div>
</section>
@endsection
