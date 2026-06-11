@extends('layouts.app')
@section('title', 'Ambulance Dispatch')

@section('content')
<section class="section">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <h1>🚑 Ambulance Dispatch</h1>
        <div>
            <a class="button" href="{{ route($routePrefix . '.ambulance.create') }}">New Request</a>
            <a class="ghost-button" href="{{ route($routePrefix . '.dashboard') }}">Dashboard</a>
        </div>
    </div>

    <x-search-filter 
        action="{{ route($routePrefix . '.ambulance.index') }}" 
        search-placeholder="Search ambulance requests by patient, phone, address..." 
        :filters="['status' => ['pending' => 'Pending', 'dispatched' => 'Dispatched', 'resolved' => 'Resolved', 'cancelled' => 'Cancelled']]" 
    />

    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif
    @if(session('status'))
        <div class="notice">{{ session('status') }}</div>
    @endif

    <div class="card" style="padding:0; overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date/Time</th>
                    <th>Patient</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td>#{{ $req->id }}</td>
                    <td>
                        {{ $req->created_at->format('M d, g:i A') }}<br>
                        <small class="muted">{{ $req->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                        <strong>{{ $req->patient_name }}</strong><br>
                        📞 <a href="tel:{{ $req->patient_phone }}">{{ $req->patient_phone }}</a>
                    </td>
                    <td>
                        @if($req->latitude && $req->longitude)
                            <a href="https://maps.google.com/?q={{ $req->latitude }},{{ $req->longitude }}" target="_blank" class="tag" style="background:#e0f2fe; color:#0369a1; text-decoration:none;">
                                📍 View Map
                            </a><br>
                        @endif
                        <small>{{ $req->address ?: 'No textual address' }}</small>
                    </td>
                    <td>
                        @if($req->status === 'pending')
                            <span class="stock-badge out-of-stock">Pending</span>
                        @elseif($req->status === 'dispatched')
                            <span class="stock-badge low-stock">Dispatched</span>
                        @elseif($req->status === 'resolved')
                            <span class="stock-badge in-stock">Resolved</span>
                        @else
                            <span class="stock-badge" style="background:var(--border); color:var(--text);">Cancelled</span>
                        @endif
                    </td>
                    <td>
                        <div class="pill-row">
                            <form method="POST" action="{{ route($routePrefix . '.ambulance.update', $req) }}" style="margin:0;">
                                @csrf
                                @method('PUT')
                                <select name="status" style="width:auto; padding: 4px 8px; margin:0;" onchange="this.form.submit()">
                                    <option value="pending" @selected($req->status === 'pending')>Pending</option>
                                    <option value="dispatched" @selected($req->status === 'dispatched')>Dispatched</option>
                                    <option value="resolved" @selected($req->status === 'resolved')>Resolved</option>
                                    <option value="cancelled" @selected($req->status === 'cancelled')>Cancelled</option>
                                </select>
                            </form>
                            <a class="ghost-button" href="{{ route($routePrefix . '.ambulance.edit', $req) }}">Edit</a>
                            <form method="POST" action="{{ route($routePrefix . '.ambulance.destroy', $req) }}" onsubmit="return confirm('Delete this ambulance request?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ghost-button" style="color: var(--error-text); border-color: var(--error-border);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding: 32px;" class="muted">No ambulance requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin: 16px;">
            {{ $requests->links() }}
        </div>
    </div>
</section>
@endsection
