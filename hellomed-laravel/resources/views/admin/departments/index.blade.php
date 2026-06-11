@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding: 0 0 16px;">
        <div>
            <h1>Departments</h1>
            <p>Add and manage hospital departments and service scopes.</p>
        </div>
        <a class="button" href="{{ route('admin.departments.create') }}">Add department</a>
    </div>

    <x-search-filter 
        action="{{ route('admin.departments.index') }}" 
        search-placeholder="Search departments by name or description..." 
    />

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Service scope</th>
                    <th>Doctors</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($departments as $department)
                    <tr>
                        <td>{{ $department->name }}</td>
                        <td>{{ $department->service_scope }}</td>
                        <td>{{ $department->doctors_count }}</td>
                        <td>{{ $department->is_active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <div class="pill-row">
                                <a class="ghost-button" href="{{ route('admin.departments.edit', $department) }}">Edit</a>
                                <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ghost-button" style="color: var(--error-text); border-color: var(--error-border);">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No departments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 20px;">{{ $departments->links() }}</div>
    </div>
</section>
@endsection
