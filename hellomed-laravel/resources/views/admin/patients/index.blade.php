@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding:0 0 16px;">
        <div>
            <h1>Manage Patients</h1>
            <p>View and manage all registered patient accounts and their profiles.</p>
        </div>
        <a class="button" href="{{ route($routePrefix . '.patients.create') }}">Add Patient</a>
    </div>

    @if (session('status'))
        <div class="card" style="margin-bottom: 16px; background: rgba(var(--primary-color-rgb), 0.1); border-color: var(--primary-color); color: var(--primary-color);">
            {{ session('status') }}
        </div>
    @endif
        
    <x-search-filter 
        action="{{ route($routePrefix . '.patients.index') }}" 
        search-placeholder="Search patients by name, email, phone..." 
        :filters="['gender' => ['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other']]" 
    />

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($patients as $patient)
                    <tr>
                        <td>{{ $patient->name }}</td>
                        <td>{{ $patient->email }}</td>
                        <td>{{ $patient->phone ?: 'N/A' }}</td>
                        <td>{{ $patient->patientProfile?->gender ?: 'N/A' }}</td>
                        <td>{{ $patient->patientProfile?->date_of_birth ? \Carbon\Carbon::parse($patient->patientProfile->date_of_birth)->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <div class="pill-row">
                                <a class="ghost-button" href="{{ route($routePrefix . '.patients.edit', $patient) }}">Edit</a>
                                <form action="{{ route($routePrefix . '.patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this patient? All associated data will be lost.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ghost-button" style="color: var(--error-text); border-color: var(--error-border);">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:20px;">{{ $patients->links() }}</div>
    </div>
</section>
@endsection
