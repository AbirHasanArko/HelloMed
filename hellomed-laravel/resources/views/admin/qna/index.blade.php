@extends('layouts.app')
@section('title', 'Moderate Q&A')

@section('content')
<section class="section">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <h1>❓ Moderate Q&A</h1>
        <a class="ghost-button" href="{{ route($routePrefix . '.dashboard') }}">Dashboard</a>
    </div>

    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    <x-search-filter 
        action="{{ route($routePrefix . '.qna.index') }}" 
        search-placeholder="Search questions by user, title..." 
        :filters="['status' => ['pending' => 'Pending', 'answered' => 'Answered', 'resolved' => 'Resolved', 'rejected' => 'Rejected']]" 
    />

    <div class="card" style="padding:0; overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Question Title</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Answers</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $q)
                <tr>
                    <td><strong>{{ str()->limit($q->title, 50) }}</strong></td>
                    <td>{{ $q->user?->name ?? 'Anonymous' }}</td>
                    <td>
                        @if($q->status === 'open')
                            <span class="stock-badge out-of-stock">Open</span>
                        @elseif($q->status === 'resolved')
                            <span class="stock-badge in-stock">Resolved</span>
                        @else
                            <span class="stock-badge" style="background:var(--border); color:var(--text);">Closed</span>
                        @endif
                    </td>
                    <td>{{ $q->answers_count }}</td>
                    <td><small class="muted">{{ $q->created_at->format('M d, Y') }}</small></td>
                    <td>
                        <div class="pill-row">
                            <a class="ghost-button" href="{{ route($routePrefix . '.qna.edit', $q) }}">View/Answer</a>
                            <form method="POST" action="{{ route($routePrefix . '.qna.destroy', $q) }}" onsubmit="return confirm('Delete this question and all answers?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ghost-button" style="color: var(--error-text); border-color: var(--error-border);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding: 32px;" class="muted">No questions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin: 16px;">
            {{ $questions->links() }}
        </div>
    </div>
</section>
@endsection
