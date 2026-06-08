@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Staff dashboard</h1>
        <p>Operational queue for staff members managing day-to-day hospital workflows.</p>
        <div class="meta-row" style="margin-bottom: 16px;">
            <a class="ghost-button" href="{{ route('admin.appointments.index') }}">Manage appointments</a>
            <a class="ghost-button" href="{{ route('admin.doctors.index') }}">Doctor schedules</a>
            <a class="ghost-button" href="{{ route('admin.articles.index') }}">Articles</a>
            <a class="ghost-button" href="{{ route('staff.offline-appointments.create') }}" style="border-color:var(--primary); color:var(--primary); background:var(--surface);">➕ Book Offline Appointment</a>
            <a class="ghost-button" href="{{ route('staff.ambulance.index') }}" @if($pendingAmbulance > 0) style="border-color:var(--error-border); color:var(--error-text); background:var(--error-bg);" @endif>
                🚑 Ambulance Dispatch @if($pendingAmbulance > 0) ({{ $pendingAmbulance }}) @endif
            </a>
        </div>
        <div class="grid cols-4">
            <div class="stat"><strong>{{ $pendingAppointments }}</strong><span class="muted">Pending appointments</span></div>
            <div class="stat"><strong>{{ $todayAppointments }}</strong><span class="muted">Today appointments</span></div>
            <div class="stat"><strong>{{ $doctorCount }}</strong><span class="muted">Active doctors</span></div>
            <div class="stat"><strong>{{ $publishedArticles }}</strong><span class="muted">Published articles</span></div>
        </div>
    </section>
@endsection
