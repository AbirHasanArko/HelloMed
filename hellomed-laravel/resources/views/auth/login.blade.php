@extends('layouts.app')
@section('title', 'Login')

@section('content')
    <section class="section">
        <div class="grid cols-2 fade-in">
            <div class="auth-sidebar">
                <div class="auth-pattern"></div>
                <div style="position:relative;z-index:1;">
                    <div class="tag">Account access</div>
                    <h1 style="font-size:2rem;">Welcome back</h1>
                    <p>Sign in to manage appointments and hospital operations based on your role.</p>
                    <svg width="120" height="120" viewBox="0 0 120 120" fill="none" style="margin-top:20px;opacity:0.3;">
                        <rect x="42" y="15" width="36" height="90" rx="8" fill="white"/>
                        <rect x="15" y="42" width="90" height="36" rx="8" fill="white"/>
                    </svg>
                </div>
            </div>
            <div class="card">
                <h2 style="margin-bottom:24px;">Login</h2>
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <label>
                        Email
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </label>
                    <label>
                        Password
                        <input type="password" name="password" required>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;">
                        <input type="checkbox" name="remember" value="1"> Remember me
                    </label>
                    <button class="button" type="submit" style="width:100%;justify-content:center;">Login</button>
                    <p class="muted" style="margin-top: 16px; text-align:center;">No account? <a href="{{ route('register') }}">Register here</a>.</p>
                </form>
            </div>
        </div>
    </section>
@endsection
