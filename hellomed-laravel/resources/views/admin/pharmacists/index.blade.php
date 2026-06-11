@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding: 0 0 16px;">
        <div>
            <h1>Pharmacists</h1>
            <p>Manage pharmacist accounts and roles.</p>
        </div>
        <a class="button" href="{{ route('admin.pharmacists.create') }}">Add Pharmacist</a>
    </div>

    <x-search-filter 
        action="{{ route('admin.pharmacists.index') }}" 
        search-placeholder="Search pharmacists by name, email, phone..." 
        :filters="['is_active' => ['1' => 'Active', '0' => 'Inactive']]" 
    />

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Monthly Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pharmacists as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>{{ $user->monthly_payment ? 'BDT ' . number_format((float)$user->monthly_payment, 2) : '-' }}</td>
                        <td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <div class="pill-row">
                                <a href="{{ route('admin.pharmacists.edit', $user) }}" class="ghost-button">Edit</a>
                                <form action="{{ route('admin.pharmacists.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this pharmacist?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ghost-button" style="color: var(--error-text); border-color: var(--error-border);">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No pharmacists found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 20px;">{{ $pharmacists->links() }}</div>
    </div>
</section>
@endsection
