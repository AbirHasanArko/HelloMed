@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="fade-in" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1>Doctors</h1>
                <p>Find doctors by department and service mode, then request an appointment online.</p>
            </div>
            <form method="GET" action="{{ route('doctors.index') }}" style="display: flex; gap: 12px; align-items: center; background: white; padding: 6px 6px 6px 16px; border-radius: 50px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid var(--border); transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 6px 16px rgba(0,180,216,0.15)'; this.style.borderColor='rgba(0,180,216,0.3)';" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'; this.style.borderColor='var(--border)';">
                <label for="department" style="margin-bottom: 0; color: var(--text-light); font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    Filter
                </label>
                <div style="position: relative;">
                    <select name="department" id="department" onchange="this.form.submit()" style="appearance: none; -webkit-appearance: none; background: var(--surface); border: none; padding: 8px 36px 8px 16px; border-radius: 40px; font-weight: 600; color: var(--text); cursor: pointer; outline: none; font-size: 0.95rem; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); transition: background 0.2s ease;">
                        <option value="">All Departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->slug }}" @selected(request('department') === $department->slug)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--text-light);"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
            </form>
        </div>
        <div class="grid cols-3">
            @foreach ($doctors as $doctor)
                <a class="card fade-in" href="{{ route('doctors.show', $doctor) }}" style="text-align:center;">
                    @if ($doctor->photo_path)
                        <img src="{{ Storage::url($doctor->photo_path) }}" alt="{{ $doctor->name }}" class="avatar-image" loading="lazy" style="margin:0 auto 12px;">
                    @else
                        <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));display:grid;place-items:center;margin:0 auto 12px;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" opacity="0.7"><path d="M8 7a4 4 0 1 0 8 0 4 4 0 1 0-8 0"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                        </div>
                    @endif
                    <div class="tag">{{ $doctor->department?->name }}</div>
                    <h3>{{ $doctor->name }}</h3>
                    <p>{{ $doctor->specialty }}</p>
                    <div class="meta-row" style="justify-content:center;">
                        @if ($doctor->online_available)
                            <span class="pill" style="background:var(--badge-green-bg);color:var(--badge-green-text);border-color:transparent;font-size:12px;padding:4px 10px;">🌐 Online</span>
                        @else
                            <span class="pill" style="font-size:12px;padding:4px 10px;">Offline only</span>
                        @endif
                        @if ($doctor->offline_available)
                            <span class="pill" style="background:var(--accent);color:var(--primary);border-color:transparent;font-size:12px;padding:4px 10px;">🏥 Offline</span>
                        @else
                            <span class="pill" style="font-size:12px;padding:4px 10px;">Online only</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        <div style="margin-top: 24px;">{{ $doctors->links() }}</div>
    </section>
@endsection
