@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding:0 0 16px;">
        <div>
            <h1>Contact Messages</h1>
            <p>View and respond to messages submitted by patients through the contact form.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="card" style="margin-bottom: 16px; background: rgba(var(--primary-color-rgb), 0.1); border-color: var(--primary-color); color: var(--primary-color);">
            {{ session('status') }}
        </div>
    @endif
        
    <div class="card">
        @if($messages->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient Name</th>
                        <th>Patient Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($messages as $message)
                        <tr>
                            <td style="white-space: nowrap;">{{ $message->created_at->format('M d, Y g:i A') }}</td>
                            <td>{{ $message->user?->name ?? 'Unknown' }}</td>
                            <td>{{ $message->user?->email ?? 'N/A' }}</td>
                            <td><strong>{{ Str::limit($message->subject, 30) }}</strong></td>
                            <td style="max-width: 300px; white-space: normal;">{{ Str::limit($message->message, 80) }}</td>
                            <td>
                                <span style="padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500; background: {{ $message->status === 'pending' ? 'var(--badge-yellow-bg)' : 'var(--badge-green-bg)' }}; color: {{ $message->status === 'pending' ? 'var(--badge-yellow-text)' : 'var(--badge-green-text)' }};">
                                    {{ ucfirst($message->status) }}
                                </span>
                            </td>
                            <td>
                                @if($message->user?->email)
                                    <a class="button" href="mailto:{{ $message->user->email }}?subject={{ rawurlencode('Re: ' . $message->subject) }}" target="_blank">
                                        Reply via Email
                                    </a>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;">No email</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 32px; text-align: center; color: var(--text-muted);">
                <p>No messages received yet.</p>
            </div>
        @endif
    </div>
</section>
@endsection
