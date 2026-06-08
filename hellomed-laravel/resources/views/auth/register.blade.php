@extends('layouts.app')
@section('title', 'Register')

@section('content')
    <section class="section">
        <div class="grid cols-2 fade-in">
            <div class="auth-sidebar">
                <div class="auth-pattern"></div>
                <div style="position:relative;z-index:1;">
                    <div class="tag">Patient onboarding</div>
                    <h1 style="font-size:2rem;">Join HelloMed</h1>
                    <p>Register as a patient to submit and track appointment requests online.</p>
                    <svg width="120" height="120" viewBox="0 0 120 120" fill="none" style="margin-top:20px;opacity:0.3;">
                        <rect x="42" y="15" width="36" height="90" rx="8" fill="white"/>
                        <rect x="15" y="42" width="90" height="36" rx="8" fill="white"/>
                        <path d="M30 60 L42 45 L52 55 L65 35 L78 50 L88 42 L95 55" stroke="white" stroke-width="2" stroke-linecap="round" fill="none" opacity="0.6"/>
                    </svg>
                </div>
            </div>
            <div class="card">
                <h2 style="margin-bottom:24px;">Create account</h2>
                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <label>
                        Name
                        <input type="text" name="name" value="{{ old('name') }}" required>
                    </label>
                    <label>
                        Email
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </label>
                    <label>
                        Password
                        <input type="password" name="password" required>
                    </label>
                    <label>
                        Confirm password
                        <input type="password" name="password_confirmation" required>
                    </label>
                    <button class="button" type="submit" style="width:100%;justify-content:center;">Create account</button>
                    <p class="muted" style="margin-top: 16px; text-align:center;">Already have an account? <a href="{{ route('login') }}">Login</a>.</p>
                </form>
            </div>
        </div>
    </section>
@endsection
