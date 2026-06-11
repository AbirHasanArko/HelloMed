@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Admin dashboard</h1>
        <div class="meta-row" style="margin-bottom: 16px;">
            <a class="ghost-button" href="{{ route('admin.appointments.index') }}">Appointments</a>
            <a class="ghost-button" href="{{ route('admin.patients.index') }}">Patients</a>
            <a class="ghost-button" href="{{ route('admin.departments.index') }}">Departments</a>
            <a class="ghost-button" href="{{ route('admin.available-tests.index') }}">Available Tests</a>
            <a class="ghost-button" href="{{ route('admin.medicines.index') }}">Pharmacy</a>
            <a class="ghost-button" href="{{ route('admin.ambulance.index') }}">Ambulance</a>
            <a class="ghost-button" href="{{ route('admin.articles.index') }}">Articles</a>
            <a class="ghost-button" href="{{ route('admin.comments.index') }}">Comments</a>
            <a class="ghost-button" href="{{ route('admin.qna.index') }}">Q&A</a>
            <a class="ghost-button" href="{{ route('admin.doctors.index') }}">Doctors</a>
            <a class="ghost-button" href="{{ route('admin.audit-logs.index') }}">Audit logs</a>
            @if (auth()->user()->isAdmin())
                <a class="ghost-button" href="{{ route('admin.payments.index') }}">Payments</a>
                <a class="ghost-button" href="{{ route('admin.staff.index') }}">Staff</a>
                <a class="ghost-button" href="{{ route('admin.pharmacists.index') }}">Pharmacists</a>
            @endif
        </div>
        <div class="grid cols-4">
            <div class="stat"><strong>{{ $doctorCount }}</strong><span class="muted">Doctors</span></div>
            <div class="stat"><strong>{{ $departmentCount }}</strong><span class="muted">Departments</span></div>
            <div class="stat"><strong>{{ $appointmentCount }}</strong><span class="muted">Appointments</span></div>
            <div class="stat"><strong>{{ $articleCount }}</strong><span class="muted">Articles</span></div>
        </div>
        <div class="grid cols-4" style="margin-top: 16px;">
            <div class="stat"><strong>{{ $paymentCount }}</strong><span class="muted">Payments</span></div>
        </div>

        <div class="card" style="margin-top: 16px;">
            <h3>Security and operations alerts (last 24 hours)</h3>
            <div class="pill-row">
                <a class="ghost-button" href="{{ route('admin.audit-logs.index', ['action' => 'auth.login_failed']) }}">Failed logins: {{ $failedLoginCount }}</a>
                <a class="ghost-button" href="{{ route('admin.audit-logs.index', ['action' => 'medicine_order.payment_callback']) }}">Failed payment callbacks: {{ $failedPaymentCallbackCount }}</a>
                <a class="ghost-button" href="{{ route('admin.audit-logs.index', ['action' => 'appointment.status_updated']) }}">Appointments with frequent status changes: {{ $frequentAppointmentStatusChanges }}</a>
            </div>
        </div>
    </section>
@endsection
