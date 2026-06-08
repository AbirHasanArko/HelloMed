@extends('layouts.app')
@section('title', 'Home')

@section('content')
    <section class="hero">
        <div class="fade-in">
            <div class="tag">✦ Whole hospital platform</div>
            <h1>Care across departments, doctors, and articles in one place.</h1>
            <p>Patients can browse specialties, choose online or offline services, request appointments, and read hospital articles without leaving the platform.</p>
            <div class="pill-row" style="margin-top: 8px;">
                <a class="button" href="{{ route('doctors.index') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7a4 4 0 1 0 8 0 4 4 0 1 0-8 0"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                    Book a doctor
                </a>
                <a class="ghost-button" href="{{ route('articles.index') }}">Read articles</a>
            </div>
        </div>
        <div class="hero-visual fade-in fade-in-delay-2">
            <div class="hero-visual-pattern"></div>
            <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="42" y="15" width="36" height="90" rx="8" fill="white" opacity="0.3"/>
                <rect x="15" y="42" width="90" height="36" rx="8" fill="white" opacity="0.3"/>
                <rect x="46" y="19" width="28" height="82" rx="6" fill="white" opacity="0.5"/>
                <rect x="19" y="46" width="82" height="28" rx="6" fill="white" opacity="0.5"/>
                <path d="M30 60 L42 45 L52 55 L65 35 L78 50 L88 42 L95 55" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none" opacity="0.8"/>
                <circle cx="60" cy="60" r="42" stroke="white" stroke-width="1" opacity="0.15" fill="none"/>
                <circle cx="60" cy="60" r="52" stroke="white" stroke-width="0.5" opacity="0.1" fill="none"/>
            </svg>
        </div>
    </section>

    <section class="section fade-in">
        <div class="grid cols-4">
            <div class="stat fade-in fade-in-delay-1">
                <strong>24/7</strong>
                <span class="muted">Booking access</span>
            </div>
            <div class="stat fade-in fade-in-delay-2">
                <strong>{{ $totalDepartments }}</strong>
                <span class="muted">Active departments</span>
            </div>
            <div class="stat fade-in fade-in-delay-3">
                <strong>{{ $totalDoctors }}</strong>
                <span class="muted">Total doctors</span>
            </div>
            <div class="stat fade-in fade-in-delay-4">
                <strong>{{ number_format((int) $patientCount) }}</strong>
                <span class="muted">Registered patients</span>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="fade-in" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <h2 style="margin-bottom: 0;">Featured Departments</h2>
            <a href="{{ route('departments.index') }}" class="button" style="background: var(--surface); color: var(--primary); border: 1px solid var(--border); padding: 8px 16px; display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.95rem; border-radius: 50px; transition: all 0.2s ease;">
                See all
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid cols-3">
            @foreach ($departments as $department)
                <a class="card photo-card fade-in" href="{{ route('departments.show', $department) }}">
                    <div class="photo-card-img">
                        @if ($department->image_path)
                            <img src="{{ Storage::url($department->image_path) }}" alt="{{ $department->name }}" loading="lazy">
                        @else
                            <div style="width:100%;height:100%;background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));display:grid;place-items:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" opacity="0.4"><path d="M3 21h18M9 8h1M9 12h1M9 16h1M14 8h1M14 12h1M14 16h1M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/></svg>
                            </div>
                        @endif
                        <div class="photo-card-overlay"></div>
                        <span class="photo-card-badge tag" style="margin-bottom:0;">{{ $department->service_scope }}</span>
                    </div>
                    <div class="photo-card-body">
                        <h3>{{ $department->name }}</h3>
                        <p>{{ $department->description }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="fade-in" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <h2 style="margin-bottom: 0;">Featured Doctors</h2>
            <a href="{{ route('doctors.index') }}" class="button" style="background: var(--surface); color: var(--primary); border: 1px solid var(--border); padding: 8px 16px; display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.95rem; border-radius: 50px; transition: all 0.2s ease;">
                See all
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid cols-4">
            @foreach ($doctors as $doctor)
                <a class="card fade-in" href="{{ route('doctors.show', $doctor) }}" style="text-align:center;">
                    @if ($doctor->photo_path)
                        <img src="{{ Storage::url($doctor->photo_path) }}" alt="{{ $doctor->name }}" class="avatar-image" loading="lazy" style="margin:0 auto 12px;">
                    @else
                        <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));display:grid;place-items:center;margin:0 auto 12px;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" opacity="0.7"><path d="M8 7a4 4 0 1 0 8 0 4 4 0 1 0-8 0"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                        </div>
                    @endif
                    <div class="tag">{{ $doctor->department?->name }}</div>
                    <h3>{{ $doctor->name }}</h3>
                    <p>{{ $doctor->specialty }}</p>
                </a>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2 class="fade-in">Latest articles</h2>
        <div class="grid cols-3">
            @foreach ($articles as $article)
                <a class="card photo-card fade-in" href="{{ route('articles.show', $article) }}">
                    <div class="photo-card-img">
                        @if ($article->cover_image_path)
                            <img src="{{ Storage::url($article->cover_image_path) }}" alt="{{ $article->title }}" loading="lazy">
                        @else
                            <div style="width:100%;height:100%;background:linear-gradient(135deg, #0d9488, #6366f1);display:grid;place-items:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" opacity="0.4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            </div>
                        @endif
                        <div class="photo-card-overlay"></div>
                        <span class="photo-card-badge tag" style="margin-bottom:0;">{{ $article->category?->name }}</span>
                    </div>
                    <div class="photo-card-body">
                        <h3>{{ $article->title }}</h3>
                        <p>{{ $article->excerpt }}</p>
                        <div class="muted" style="font-size:12px;margin-top:auto;">Writer: {{ $article->author?->doctorProfile?->name ?? $article->author?->name ?? 'HelloMed Team' }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection
