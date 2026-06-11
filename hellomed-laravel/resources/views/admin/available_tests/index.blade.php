@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding: 0 0 16px;">
        <div>
            <h1>Available Diagnostics Services</h1>
            <p>Manage the hospital's diagnostics services and diagnostic services catalog.</p>
        </div>
        <a href="{{ route('admin.available-tests.create') }}" class="button">Add New Test</a>
    </div>

    <x-search-filter 
        action="{{ route('admin.available-tests.index') }}" 
        search-placeholder="Search tests by name, code, category..." 
        :filters="[]" 
    />

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Lab Room</th>
                    <th>Fee (BDT)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tests as $test)
                    <tr>
                        <td><strong>{{ $test->name }}</strong></td>
                        <td>{{ $test->lab_room_number ?: 'N/A' }}</td>
                        <td>{{ number_format($test->fee_bdt, 2) }}</td>
                        <td>
                            @if($test->is_active)
                                <span class="stock-badge in-stock">Active</span>
                            @else
                                <span class="stock-badge out-of-stock">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="pill-row">
                                <a class="ghost-button" href="{{ route('admin.available-tests.edit', $test) }}">Edit</a>
                                <form action="{{ route('admin.available-tests.destroy', $test) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this test?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ghost-button" style="color: var(--error-text); border-color: var(--error-border);">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No available tests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 20px;">{{ $tests->links() }}</div>
    </div>
</section>
@endsection
