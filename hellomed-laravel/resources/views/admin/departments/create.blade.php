@extends('layouts.app')

@section('content')
<section class="section">
    <h1>Add department</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.departments.store') }}" enctype="multipart/form-data">
            @csrf
            <label>
                Department name
                <input type="text" name="name" value="{{ old('name') }}" required>
            </label>
            <label>
                Service scope
                <input type="text" name="service_scope" value="{{ old('service_scope') }}" placeholder="online and offline" required>
            </label>
            <label>
                Description
                <textarea name="description" required>{{ old('description') }}</textarea>
            </label>
            <label>
                Department image
                <input type="file" name="image" accept="image/*">
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
            
            <label>
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                Active department
            </label>
            <button class="button" type="submit">Create department</button>
        </form>
    </div>
</section>
@endsection
