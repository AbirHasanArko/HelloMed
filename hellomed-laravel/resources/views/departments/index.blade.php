@extends('layouts.app')
@section('title', 'Care')

@section('content')
    <section class="section">
        <div class="fade-in" style="margin-bottom: 24px;">
            <h1>Departments</h1>
            <p>Browse the hospital's care areas, from emergency and medicine to orthopedics, dentistry, and cardiology.</p>
        </div>
        <div class="grid cols-2">
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
                        <div style="display:flex;align-items:center;gap:6px;margin-top:auto;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M8 7a4 4 0 1 0 8 0 4 4 0 1 0-8 0"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                            <span class="muted" style="font-size:13px;">{{ $department->doctors_count }} doctors</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection
