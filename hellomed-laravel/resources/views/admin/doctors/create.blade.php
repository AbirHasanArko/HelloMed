@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Add new doctor</h1>
        <div class="card">
            <form method="POST" action="{{ route('admin.doctors.store') }}" enctype="multipart/form-data">
                @csrf
                <label>
                    Doctor login email
                    <input type="email" name="doctor_email" value="{{ old('doctor_email') }}" required>
                </label>
                <label>
                    Initial password
                    <input type="text" name="initial_password" value="{{ old('initial_password') }}" required>
                </label>
                <label>
                    Department
                    <select name="department_id" required>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected((int) old('department_id') === $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Name
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </label>
                <label>
                    Specialty
                    <input type="text" name="specialty" value="{{ old('specialty') }}" required>
                </label>
                <label>
                    Qualification
                    <input type="text" name="qualification" value="{{ old('qualification') }}">
                </label>
                <label>
                    Professional bio
                    <textarea name="bio">{{ old('bio') }}</textarea>
                </label>
                <label>
                    Doctor photo
                    <input type="file" name="photo" accept="image/*">
                </label>
                <label>
                    Experience years
                    <input type="number" name="experience_years" min="0" max="80" value="{{ old('experience_years', 0) }}" required>
                </label>
                <label>
                    Consultation fee
                    <input type="number" name="consultation_fee" min="0" step="0.01" value="{{ old('consultation_fee', 0) }}" required>
                </label>
                <label>
                    Online fee
                    <input type="number" name="online_fee" min="0" step="0.01" value="{{ old('online_fee') }}">
                </label>
                <label>
                    Offline fee
                    <input type="number" name="offline_fee" min="0" step="0.01" value="{{ old('offline_fee') }}">
                </label>
                <label>
                    Clinic address
                    <input type="text" name="clinic_address" value="{{ old('clinic_address') }}">
                </label>

                <label>
                    Online available days
                    <select name="online_available_days[]" multiple size="7">
                        @foreach (['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                            <option value="{{ $day }}" @selected(in_array($day, old('online_available_days', []), true))>{{ ucfirst($day) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Online available from
                    <input type="time" name="online_available_from" value="{{ old('online_available_from') }}">
                </label>
                <label>
                    Online available to
                    <input type="time" name="online_available_to" value="{{ old('online_available_to') }}">
                </label>

                <label>
                    Offline available days
                    <select name="offline_available_days[]" multiple size="7">
                        @foreach (['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                            <option value="{{ $day }}" @selected(in_array($day, old('offline_available_days', []), true))>{{ ucfirst($day) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Offline available from
                    <input type="time" name="offline_available_from" value="{{ old('offline_available_from') }}">
                </label>
                <label>
                    Offline available to
                    <input type="time" name="offline_available_to" value="{{ old('offline_available_to') }}">
                </label>

                <label>
                    Slot minutes
                    <select name="slot_minutes" required>
                        @foreach ([15,20,30,45,60] as $slot)
                            <option value="{{ $slot }}" @selected((int) old('slot_minutes', 30) === $slot)>{{ $slot }}</option>
                        @endforeach
                    </select>
                </label>

                <div style="margin: 20px 0; padding: 15px; border: 1px solid var(--border); border-radius: 8px;">
                    <h4 style="margin-top: 0;">Home Page Featured Settings</h4>
                    <label style="margin-bottom: 10px;">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))>
                        Show on Home Page
                    </label>
                    <label style="margin-bottom: 0;">
                        Display Order (e.g. 1, 2, 3)
                        <input type="number" name="featured_order" value="{{ old('featured_order', 0) }}" min="0">
                    </label>
                </div>

                <label><input type="checkbox" name="online_available" value="1" @checked(old('online_available', true))> Online available</label>
                <label><input type="checkbox" name="offline_available" value="1" @checked(old('offline_available', true))> Offline available</label>
                <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Active profile</label>

                <button class="button" type="submit">Create doctor account</button>
            </form>
        </div>
    </section>
@endsection
