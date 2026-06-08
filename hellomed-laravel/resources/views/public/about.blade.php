@extends('layouts.app')
@section('title', 'About')

@section('content')
    <section class="section">
        <div class="about-hero fade-in">
            <div class="about-hero-pattern"></div>
            <div style="position:relative;z-index:1;">
                <div class="tag" style="background:rgba(255,255,255,0.2);color:white;margin:0 auto 16px;display:inline-flex;">About HelloMed Hospital</div>
                <h1>Integrated hospital care for patients, doctors, and departments</h1>
                <p style="max-width:650px;margin:0 auto;">HelloMed is designed as a complete digital hospital platform for online and offline appointments, medicine access, professional prescriptions, and patient communication.</p>
            </div>
        </div>
    </section>

    <section class="section fade-in">
        <div class="grid cols-3">
            <div class="card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </div>
                <h3>Patient-first service</h3>
                <p>From booking to follow-up, each step is streamlined for transparent and reliable care.</p>
            </div>
            <div class="card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10,9 9,9 8,9"/>
                    </svg>
                </div>
                <h3>Doctor productivity</h3>
                <p>Doctors can manage schedules, publish articles, write structured prescriptions, and continue care through appointment chat.</p>
            </div>
            <div class="card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/>
                        <line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                </div>
                <h3>Hospital operations</h3>
                <p>Admin and staff teams manage departments, doctors, and publication workflows while pharmacist teams maintain medicine operations.</p>
            </div>
        </div>
    </section>

    <section class="section fade-in">
        <h2 style="margin-bottom:20px;">Our Platform</h2>
        <p style="margin-bottom:20px;">HelloMed connects care teams across departments so patients can book consultations, get verified doctor guidance, receive structured prescriptions, and purchase medicines from the hospital pharmacy with confidence.</p>
        <div class="photo-gallery">
            <img src="{{ asset('images/hospital-exterior.png') }}" alt="Modern hospital facility" loading="lazy">
            <img src="{{ asset('images/doctor-consultation.png') }}" alt="Doctor-patient consultation" loading="lazy">
            <img src="{{ asset('images/digital-health.png') }}" alt="Digital health platform" loading="lazy">
        </div>
    </section>

    <section class="section fade-in">
        <div class="card" style="background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));color:white;text-align:center;padding:40px;">
            <h2 style="color:white;-webkit-text-fill-color:white;background:none;">Our Mission</h2>
            <p style="color:rgba(255,255,255,0.9);max-width:600px;margin:0 auto;font-size:1.05rem;line-height:1.8;">
                To deliver seamless, transparent, and accessible healthcare through technology — bridging the gap between patients, doctors, and hospital operations in one unified digital platform.
            </p>
        </div>
    </section>
@endsection


