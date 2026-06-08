@extends('layouts.app')

@section('content')
    <section class="section fade-in">
        <div class="card" style="padding:0;overflow:hidden;">
            @if ($department->image_path)
                <div style="position:relative;width:100%;height:280px;overflow:hidden;">
                    <img src="{{ Storage::url($department->image_path) }}" alt="{{ $department->name }}" style="width:100%;height:100%;object-fit:cover;">
                    <div style="position:absolute;inset:0;background:var(--overlay-gradient);"></div>
                    <div style="position:absolute;bottom:24px;left:28px;right:28px;">
                        <span class="tag" style="background:rgba(255,255,255,0.2);color:white;margin-bottom:8px;">Department</span>
                        <h1 style="color:white;-webkit-text-fill-color:white;background:none;">{{ $department->name }}</h1>
                    </div>
                </div>
                <div style="padding:28px;">
                    <p>{{ $department->description }}</p>
                </div>
            @else
                <div style="padding:28px;">
                    <div class="tag">Department</div>
                    <h1>{{ $department->name }}</h1>
                    <p>{{ $department->description }}</p>
                </div>
            @endif
        </div>
    </section>

    <section class="section">
        <h2 class="fade-in">Doctors in this department</h2>
        <div class="grid cols-3">
            @forelse ($department->doctors as $doctor)
                <a class="card fade-in" href="{{ route('doctors.show', $doctor) }}" style="text-align:center;">
                    @if ($doctor->photo_path)
                        <img src="{{ Storage::url($doctor->photo_path) }}" alt="{{ $doctor->name }}" class="avatar-image" loading="lazy" style="margin:0 auto 12px;">
                    @else
                        <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));display:grid;place-items:center;margin:0 auto 12px;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" opacity="0.7"><path d="M8 7a4 4 0 1 0 8 0 4 4 0 1 0-8 0"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                        </div>
                    @endif
                    <h3>{{ $doctor->name }}</h3>
                    <p>{{ $doctor->specialty }}</p>
                    <div class="muted" style="font-size:13px;">{{ $doctor->online_available ? '🌐 Online' : '' }} {{ $doctor->offline_available ? '🏥 Offline' : '' }}</div>
                </a>
            @empty
                <div class="card fade-in" style="grid-column:1/-1;">
                    <p class="muted" style="margin:0;text-align:center;">No active doctors have been assigned to this department yet.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
