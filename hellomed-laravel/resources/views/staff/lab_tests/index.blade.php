@extends('layouts.app')

@section('title', 'Lab Test Requests')

@section('content')
    <section class="section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h1>Lab Test Requests</h1>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('staff.lab-tests.index', ['status' => 'pending']) }}" class="{{ $currentStatus === 'pending' ? 'button' : 'ghost-button' }}">Pending</a>
                <a href="{{ route('staff.lab-tests.index', ['status' => 'completed']) }}" class="{{ $currentStatus === 'completed' ? 'button' : 'ghost-button' }}">Completed</a>
            </div>
        </div>

        @if (session('status'))
            <div class="notice" style="margin-bottom: 20px;">
                <strong>Success!</strong> {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="error" style="margin-bottom: 20px;">
                <strong>Error!</strong>
                <ul style="margin-top: 4px; margin-left: 16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="card" style="margin-bottom: 24px; padding: 16px;">
            <strong style="display: block; margin-bottom: 12px;">Filter Requests</strong>
            <form method="GET" action="{{ route('staff.lab-tests.index') }}" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
                <input type="hidden" name="status" value="{{ $currentStatus }}">
                
                <label style="flex: 1; min-width: 150px; margin-bottom: 0;">
                    Patient Name
                    <input type="text" name="patient_name" list="patient-names" value="{{ $filters['patient_name'] ?? '' }}" placeholder="Type to search...">
                </label>
                
                <label style="flex: 1; min-width: 150px; margin-bottom: 0;">
                    Doctor Name
                    <input type="text" name="doctor_name" list="doctor-names" value="{{ $filters['doctor_name'] ?? '' }}" placeholder="Type to search...">
                </label>
                
                <label style="flex: 1; min-width: 150px; margin-bottom: 0;">
                    Test Name
                    <input type="text" name="test_name" list="test-names" value="{{ $filters['test_name'] ?? '' }}" placeholder="Type to search...">
                </label>

                <label style="flex: 1; min-width: 130px; margin-bottom: 0;">
                    Date
                    <input type="date" name="date" value="{{ $filters['date'] ?? '' }}">
                </label>

                <div style="display: flex; gap: 8px;">
                    <button class="button" type="submit">Filter</button>
                    @if(array_filter($filters))
                        <a href="{{ route('staff.lab-tests.index', ['status' => $currentStatus]) }}" class="ghost-button">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <datalist id="patient-names">
            @foreach($patientNames as $name)
                <option value="{{ $name }}">{{ $name }}</option>
            @endforeach
        </datalist>
        <datalist id="doctor-names">
            @foreach($doctorNames as $name)
                <option value="{{ $name }}">{{ $name }}</option>
            @endforeach
        </datalist>
        <datalist id="test-names">
            @foreach($availableTests as $name)
                <option value="{{ $name }}">{{ $name }}</option>
            @endforeach
        </datalist>

        <!-- List -->
        <div class="card">
            @if($currentStatus === 'pending')
                <div class="tag">Pending Uploads</div>
            @else
                <div class="tag">Completed Tests</div>
            @endif

            <div class="list" style="margin-top: 16px;">
                @forelse($labTests as $test)
                    <div class="list-item" style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <strong style="font-size: 16px; color: var(--primary-strong);">{{ $test->test_name }}</strong>
                                <p style="margin-top: 4px;"><strong>Patient:</strong> {{ $test->appointment->patient_name }} ({{ $test->appointment->patient_phone }})</p>
                                <p><strong>Doctor:</strong> Dr. {{ $test->appointment->doctor->user->name ?? 'Unknown' }} ({{ $test->appointment->doctor->department->name ?? 'Unknown' }})</p>
                                <p><strong>Requested:</strong> {{ $test->created_at->format('M d, Y h:i A') }}</p>
                                @if($test->notes)
                                    <p class="muted" style="margin-top: 4px; padding-left: 8px; border-left: 2px solid var(--border);">Note: {{ $test->notes }}</p>
                                @endif
                            </div>
                            
                            @if($test->status === 'completed')
                                <div>
                                    <span class="badge" style="background: var(--badge-green-bg); color: var(--badge-green-text); padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; margin-bottom: 8px; display: inline-block;">Completed</span>
                                    <br>
                                    <a href="{{ route('lab-tests.download', $test) }}" target="_blank" class="ghost-button">Download Result</a>
                                </div>
                            @endif
                        </div>

                        @if($test->status === 'pending')
                            @if($test->payment_status === 'unpaid')
                                <div style="background: var(--error-bg); padding: 16px; border-radius: 8px; border: 1px solid var(--error-border); margin-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                                    <strong style="color: var(--error-text);">Payment Required Before Upload</strong>
                                    <form method="POST" action="{{ route('staff.lab-tests.mark-paid', $test) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="button" type="submit" style="background: var(--primary); color: white;" onclick="return confirm('Confirm payment received for this test?')">Mark as Paid</button>
                                    </form>
                                </div>
                            @else
                                <div style="background: var(--bg); padding: 16px; border-radius: 8px; border: 1px solid var(--border-light); margin-top: 8px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <strong>Upload Result Document</strong>
                                        <span style="color: var(--badge-green-text); font-weight: bold; font-size: 12px;">✓ Paid</span>
                                    </div>
                                    <form method="POST" action="{{ route('staff.lab-tests.upload', $test) }}" enctype="multipart/form-data" style="display: flex; gap: 12px; align-items: flex-end;">
                                        @csrf
                                        <label style="flex: 1; margin-bottom: 0;">
                                            File (PDF, JPG, PNG) max 5MB
                                            <input type="file" name="result_file" accept=".pdf,.jpg,.jpeg,.png" required style="background: var(--surface);">
                                        </label>
                                        <button class="button" type="submit">Upload & Complete</button>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                @empty
                    <div class="list-item" style="text-align: center; color: var(--muted); padding: 32px 0;">
                        No {{ $currentStatus }} lab test requests found matching your filters.
                    </div>
                @endforelse
            </div>

            @if($labTests->hasPages())
                <div style="margin-top: 20px;">
                    {{ $labTests->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
