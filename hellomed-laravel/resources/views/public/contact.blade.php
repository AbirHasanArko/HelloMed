@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="grid cols-2 fade-in">
            <div class="card">
                <div class="tag">Contact hospital</div>
                <h1>Get in touch</h1>
                <p>Use this page for appointments, department inquiries, and general hospital support.</p>
                <div class="list" style="margin-top:8px;">
                    <div class="list-item" style="display:flex;gap:12px;align-items:center;">
                        <div style="width:40px;height:40px;border-radius:12px;background:var(--badge-red-bg);display:grid;place-items:center;flex-shrink:0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--badge-red-text)" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div>
                            <strong>Emergency</strong>
                            <p style="margin:0;">999 or the hospital emergency desk</p>
                        </div>
                    </div>
                    <div class="list-item" style="display:flex;gap:12px;align-items:center;">
                        <div style="width:40px;height:40px;border-radius:12px;background:var(--accent);display:grid;place-items:center;flex-shrink:0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div>
                            <strong>Reception</strong>
                            <p style="margin:0;">+880 1234 567890</p>
                        </div>
                    </div>
                    <div class="list-item" style="display:flex;gap:12px;align-items:center;">
                        <div style="width:40px;height:40px;border-radius:12px;background:var(--accent);display:grid;place-items:center;flex-shrink:0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div>
                            <strong>Email</strong>
                            <p style="margin:0;">care@hellomed.test</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card fade-in fade-in-delay-1">
                <div style="display:flex;gap:10px;align-items:center;margin-bottom:16px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                    <h3 style="margin:0;">Visit hours</h3>
                </div>
                <div class="list">
                    <div class="list-item">
                        <strong>Saturday to Thursday</strong>
                        <p style="margin:0;">8:00 AM - 10:00 PM</p>
                    </div>
                    <div class="list-item">
                        <strong>Friday</strong>
                        <p style="margin:0;">Emergency only</p>
                    </div>
                </div>
                <p style="margin-top:16px;">Offline services can be booked online through doctor profiles and appointment forms.</p>
                <a class="button" href="{{ route('doctors.index') }}" style="margin-top:8px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7a4 4 0 1 0 8 0 4 4 0 1 0-8 0"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                    Find a doctor
                </a>
            </div>
        </div>
    </section>
@endsection
