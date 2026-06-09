@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Doctor schedules</h1>
        <p>Manage doctor availability windows, working days, and service channels.</p>
        <div class="meta-row" style="margin-bottom: 16px;">
            <a class="button" href="{{ route('admin.doctors.create') }}">Add new doctor</a>
        </div>
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Department</th>
                        <th>Availability</th>
                        <th>Slot</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($doctors as $doctor)
                        <tr>
                            <td>{{ $doctor->name }}</td>
                            <td>{{ $doctor->department?->name }}</td>
                            <td>
                                @if($doctor->online_available && $doctor->online_available_from)
                                    <div style="font-size: 0.9em; margin-bottom: 4px;">
                                        <span style="display:inline-block; margin-right:4px; font-weight:600; color:var(--text);">Online:</span> 
                                        {{ \Carbon\Carbon::parse($doctor->online_available_from)->format('h:i A') }} - {{ \Carbon\Carbon::parse($doctor->online_available_to)->format('h:i A') }}
                                    </div>
                                @endif
                                @if($doctor->offline_available && $doctor->offline_available_from)
                                    <div style="font-size: 0.9em; margin-bottom: 4px;">
                                        <span style="display:inline-block; margin-right:4px; font-weight:600; color:var(--text);">Offline:</span> 
                                        {{ \Carbon\Carbon::parse($doctor->offline_available_from)->format('h:i A') }} - {{ \Carbon\Carbon::parse($doctor->offline_available_to)->format('h:i A') }}
                                    </div>
                                @endif
                                @if(!$doctor->online_available_from && !$doctor->offline_available_from)
                                    <div style="font-size: 0.9em;">
                                        <span style="display:inline-block; margin-right:4px; font-weight:600; color:var(--text);">General:</span> 
                                        {{ $doctor->available_from ? \Carbon\Carbon::parse($doctor->available_from)->format('h:i A') : 'N/A' }} - {{ $doctor->available_to ? \Carbon\Carbon::parse($doctor->available_to)->format('h:i A') : 'N/A' }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $doctor->slot_minutes }} min</td>
                            <td>
                                <div class="pill-row">
                                    <a class="ghost-button" href="{{ route('admin.doctors.edit', $doctor) }}">Edit</a>
                                    <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this doctor?');">
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
            <div style="margin-top: 20px;">{{ $doctors->links() }}</div>
        </div>
    </section>
@endsection
