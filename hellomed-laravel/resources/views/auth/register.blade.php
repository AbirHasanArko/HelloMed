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
                {{-- Prominent "Already have an account" banner --}}
                <a href="{{ route('login') }}" style="
                    display:flex; align-items:center; justify-content:space-between;
                    gap:12px; padding:14px 18px; border-radius:12px; margin-bottom:24px;
                    background: linear-gradient(135deg, var(--primary-light), var(--accent));
                    border: 1px solid var(--accent-strong); text-decoration:none;
                    transition: box-shadow 0.2s, transform 0.2s;
                " onmouseover="this.style.boxShadow='var(--shadow-glow)';this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.boxShadow='none';this.style.transform='none'">
                    <div>
                        <div style="font-weight:700; font-size:14px; color:var(--primary-strong);">Already have an account?</div>
                        <div style="font-size:12px; color:var(--primary); margin-top:2px;">Sign in to continue →</div>
                    </div>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;opacity:0.8;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                </a>
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
                    <p class="muted" style="margin-top: 16px; text-align:center;">Already have an account? <a href="{{ route('login') }}" style="font-weight:600;color:var(--primary);">Log In</a></p>
                </form>

            </div>
        </div>
    </section>
@endsection
