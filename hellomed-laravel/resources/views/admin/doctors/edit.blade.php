@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Edit doctor schedule</h1>
        <div class="card">
            <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @if ($errors->any())
                    <div class="error-box">
                        <ul style="margin-bottom:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <label>
                    Department
                    <select name="department_id" required>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected($doctor->department_id === $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Name
                    <input type="text" name="name" value="{{ old('name', $doctor->name) }}" required>
                </label>
                <label>
                    Phone number
                    <input type="text" name="phone" value="{{ old('phone', $doctor->user?->phone) }}">
                </label>
                <label>
                    Specialty
                    <input type="text" name="specialty" value="{{ old('specialty', $doctor->specialty) }}" required>
                </label>
                <label>
                    Qualification
                    <input type="text" name="qualification" value="{{ old('qualification', $doctor->qualification) }}">
                </label>
                <label>
                    Doctor photo
                    <input type="file" name="photo" accept="image/*">
                </label>
                @if ($doctor->photo_path)
                    <p><img src="{{ Storage::url($doctor->photo_path) }}" alt="{{ $doctor->name }}" style="width: 120px; height: 120px; object-fit: cover; border-radius: 14px;"></p>
                @endif
                <label>
                    Experience years
                    <input type="number" name="experience_years" min="0" max="80" value="{{ old('experience_years', $doctor->experience_years) }}" required>
                </label>
                <label>
                    Consultation fee
                    <input type="number" name="consultation_fee" min="0" step="0.01" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" required>
                </label>
                <label>
                    Online fee
                    <input type="number" name="online_fee" min="0" step="0.01" value="{{ old('online_fee', $doctor->online_fee) }}">
                </label>
                <label>
                    Offline fee
                    <input type="number" name="offline_fee" min="0" step="0.01" value="{{ old('offline_fee', $doctor->offline_fee) }}">
                </label>
                <label>
                    Clinic address
                    <input type="text" name="clinic_address" value="{{ old('clinic_address', $doctor->clinic_address) }}">
                </label>
                <label>
                    Available days
                    <select name="available_days[]" multiple size="7">
                        @foreach (['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                            <option value="{{ $day }}" @selected(in_array($day, old('available_days', $doctor->available_days ?? []), true))>{{ ucfirst($day) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Available from
                    <input type="time" name="available_from" value="{{ old('available_from', $doctor->available_from ? \Carbon\Carbon::parse($doctor->available_from)->format('H:i') : '') }}">
                </label>
                <label>
                    Available to
                    <input type="time" name="available_to" value="{{ old('available_to', $doctor->available_to ? \Carbon\Carbon::parse($doctor->available_to)->format('H:i') : '') }}">
                </label>

                <h3>Online schedule</h3>
                <label>
                    Online available days
                    <select name="online_available_days[]" multiple size="7">
                        @foreach (['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                            <option value="{{ $day }}" @selected(in_array($day, old('online_available_days', $doctor->online_available_days ?? []), true))>{{ ucfirst($day) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Online available from
                    <input type="time" name="online_available_from" value="{{ old('online_available_from', $doctor->online_available_from ? \Carbon\Carbon::parse($doctor->online_available_from)->format('H:i') : '') }}">
                </label>
                <label>
                    Online available to
                    <input type="time" name="online_available_to" value="{{ old('online_available_to', $doctor->online_available_to ? \Carbon\Carbon::parse($doctor->online_available_to)->format('H:i') : '') }}">
                </label>

                <h3>Offline chamber schedule</h3>
                <label>
                    Offline available days
                    <select name="offline_available_days[]" multiple size="7">
                        @foreach (['sunday','monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                            <option value="{{ $day }}" @selected(in_array($day, old('offline_available_days', $doctor->offline_available_days ?? []), true))>{{ ucfirst($day) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Offline available from
                    <input type="time" name="offline_available_from" value="{{ old('offline_available_from', $doctor->offline_available_from ? \Carbon\Carbon::parse($doctor->offline_available_from)->format('H:i') : '') }}">
                </label>
                <label>
                    Offline available to
                    <input type="time" name="offline_available_to" value="{{ old('offline_available_to', $doctor->offline_available_to ? \Carbon\Carbon::parse($doctor->offline_available_to)->format('H:i') : '') }}">
                </label>
                <label>
                    Slot minutes
                    <select name="slot_minutes" required>
                        @foreach ([15,20,30,45,60] as $slot)
                            <option value="{{ $slot }}" @selected((int) old('slot_minutes', $doctor->slot_minutes) === $slot)>{{ $slot }}</option>
                        @endforeach
                    </select>
                </label>
                <div style="margin: 20px 0; padding: 15px; border: 1px solid var(--border); border-radius: 8px;">
                    <h4 style="margin-top: 0;">Home Page Featured Settings</h4>
                    <label style="margin-bottom: 10px;">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $doctor->is_featured))>
                        Show on Home Page
                    </label>
                    <label style="margin-bottom: 0;">
                        Display Order (e.g. 1, 2, 3)
                        <input type="number" name="featured_order" value="{{ old('featured_order', $doctor->featured_order) }}" min="0">
                    </label>
                </div>

                <label><input type="checkbox" name="online_available" value="1" @checked(old('online_available', $doctor->online_available))> Online available</label>
                <label><input type="checkbox" name="offline_available" value="1" @checked(old('offline_available', $doctor->offline_available))> Offline available</label>
                <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $doctor->is_active))> Active profile</label>
                <label>
                    New password <span class="muted">(leave blank to keep current)</span>
                    <input type="text" name="password" value="{{ old('password') }}">
                </label>
                <button class="button" type="submit">Save doctor schedule</button>
            </form>
        </div>
    </section>
@endsection
