@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding: 0 0 16px;">
        <div>
            <h1>Edit Lab Test</h1>
            <p>Modify existing lab test or diagnostic service details.</p>
        </div>
        <a class="ghost-button" href="{{ route('admin.available-tests.index') }}">← Back</a>
    </div>

    <div class="card fade-in">
        <form method="POST" action="{{ route('admin.available-tests.update', $availableTest) }}">
            @csrf
            @method('PUT')
            
            <div class="grid cols-2">
                <label>
                    Test Name *
                    <input type="text" name="name" value="{{ old('name', $availableTest->name) }}" required>
                </label>
                
                <label>
                    Fee (BDT) *
                    <input type="number" step="0.01" name="fee_bdt" value="{{ old('fee_bdt', $availableTest->fee_bdt) }}" required>
                </label>
                
                <label>
                    Lab Room Number
                    <input type="text" name="lab_room_number" value="{{ old('lab_room_number', $availableTest->lab_room_number) }}">
                </label>
                
                <label>
                    Location Description
                    <input type="text" name="location" value="{{ old('location', $availableTest->location) }}" placeholder="e.g. Main Building, Floor 2">
                </label>
            </div>
            
            <label style="margin-top: 16px;">
                Description
                <textarea name="description">{{ old('description', $availableTest->description) }}</textarea>
            </label>
            
            <label style="margin-top: 16px; display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $availableTest->is_active) ? 'checked' : '' }}>
                Is Active (Visible to public)
            </label>

            <div style="margin-top: 24px;">
                <button type="submit" class="button">Update Test</button>
            </div>
        </form>
    </div>
</section>
@endsection
