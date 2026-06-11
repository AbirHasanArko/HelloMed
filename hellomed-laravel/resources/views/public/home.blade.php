@extends('layouts.app')
@section('title', 'Home')

@section('content')
    <section class="hero">
        <div class="hero-text fade-in">
            <div class="tag">✦ AI powered digital hospital platform</div>
            <h1>Care across departments, doctors, medicines, and articles in one place.</h1>
            <p>Patients can browse specialties, choose online or offline services, request appointments, order medicines, and read hospital articles without leaving the platform.</p>
        </div>
        <div class="hero-cards fade-in fade-in-delay-1">
            <style>
                .action-cards {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                    gap: 20px;
                    margin-top: 24px;
                }
                .action-card {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    padding: 32px 24px;
                    text-align: center;
                    gap: 16px;
                    transition: all 0.3s ease;
                    text-decoration: none;
                    border-radius: 12px;
                    background: var(--surface);
                    border: 1px solid var(--border);
                }
                .action-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 12px 24px rgba(0,0,0,0.1);
                    border-color: var(--primary);
                }
                .action-card-icon {
                    width: 64px;
                    height: 64px;
                    border-radius: 50%;
                    background: rgba(13, 148, 136, 0.1); /* fallback var(--primary-light) */
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--primary);
                }
                .action-card span {
                    font-weight: 600;
                    color: var(--text);
                    font-size: 1.1rem;
                }
            </style>
            <div class="action-cards">
                <a class="card action-card fade-in" href="{{ route('doctors.index') }}">
                    <div class="action-card-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M12 9v6M9 12h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span>Book a doctor</span>
                </a>
                <a class="card action-card fade-in fade-in-delay-1" href="{{ route('medicines.index') }}">
                    <div class="action-card-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="7" y="10" width="10" height="12" rx="2" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 6h6M10 6v4M14 6v4M6 10h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M12 14v4M10 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span>Order Medicine</span>
                </a>
                <a class="card action-card fade-in fade-in-delay-2" href="{{ route('articles.index') }}">
                    <div class="action-card-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 19.5C4 18.1193 5.11929 17 6.5 17H20V4C20 2.89543 19.1046 2 18 2H6.5C5.11929 2 4 3.11929 4 4.5V19.5ZM4 19.5C4 20.8807 5.11929 22 6.5 22H20V19.5H6.5C5.80964 19.5 5.25 18.9404 5.25 18.25C5.25 17.5596 5.80964 17 6.5 17" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 7h6M8 11h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span>Read articles</span>
                </a>
                @auth
                    @if(auth()->user()->role === 'patient')
                        @php
                            $upcomingAppointmentsCount = auth()->user()->appointments()->where('scheduled_for', '>=', now())->count();
                        @endphp
                        @if($upcomingAppointmentsCount > 0)
                            <a class="card action-card fade-in fade-in-delay-3" href="{{ route('patient.appointments') }}">
                                <div class="action-card-icon">
                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="6" width="18" height="15" rx="2" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 3v4M16 3v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M15 14l-4 4-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <span>Upcoming Appointments <span style="background: var(--primary); color: white; border-radius: 12px; padding: 2px 8px; font-size: 12px; margin-left: 4px; vertical-align: middle;">{{ $upcomingAppointmentsCount }}</span></span>
                            </a>
                        @endif
                    @endif
                @endauth
            </div>
        </div>
        <div class="hero-visual fade-in fade-in-delay-2">
            <div class="hero-visual-pattern"></div>
            <!-- Decorative blur blobs -->
            <div style="position: absolute; top: -20%; left: -20%; width: 60%; height: 60%; background: var(--accent); border-radius: 50%; filter: blur(60px); opacity: 0.4;"></div>
            <div style="position: absolute; bottom: -20%; right: -20%; width: 60%; height: 60%; background: var(--primary-light); border-radius: 50%; filter: blur(60px); opacity: 0.3;"></div>

            <!-- Glass Card -->
            <div style="position: relative; z-index: 10; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 24px; padding: 32px; color: white; width: 85%; max-width: 340px; box-shadow: 0 24px 48px rgba(0, 0, 0, 0.15);">
                
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <div style="width: 48px; height: 48px; background: white; border-radius: 14px; display: grid; place-items: center; color: var(--primary); box-shadow: 0 8px 16px rgba(0,0,0,0.1);">
                        <svg width="28" height="28" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="9" y="3" width="8" height="20" rx="2" fill="currentColor"/>
                            <rect x="3" y="9" width="20" height="8" rx="2" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.3rem; color: white; letter-spacing: 0.5px;">HelloMed</h3>
                        <span style="font-size: 0.9rem; color: rgba(255,255,255,0.85);">Digital Hospital Hub</span>
                    </div>
                </div>
                
                <div style="background: rgba(0, 0, 0, 0.15); border-radius: 16px; padding: 20px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 0.95rem; color: rgba(255,255,255,0.9);">Patient Satisfaction</span>
                        <span style="font-weight: 800; color: var(--accent); font-size: 1.1rem;">98.5%</span>
                    </div>
                    <div style="height: 6px; background: rgba(255, 255, 255, 0.2); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; width: 98.5%; background: var(--accent); box-shadow: 0 0 10px var(--accent);"></div>
                    </div>
                </div>

                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 16px;">
                    <li style="display: flex; align-items: center; gap: 12px; font-size: 1rem; color: rgba(255,255,255,0.95); font-weight: 500;">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 24px; height: 24px; display: grid; place-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        </div>
                        24/7 Consultations
                    </li>
                    <li style="display: flex; align-items: center; gap: 12px; font-size: 1rem; color: rgba(255,255,255,0.95); font-weight: 500;">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 24px; height: 24px; display: grid; place-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        </div>
                        Verified Specialists
                    </li>
                    <li style="display: flex; align-items: center; gap: 12px; font-size: 1rem; color: rgba(255,255,255,0.95); font-weight: 500;">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 24px; height: 24px; display: grid; place-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        </div>
                        Medicine Delivery
                    </li>
                </ul>
            </div>
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
