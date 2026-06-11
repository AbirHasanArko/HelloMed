@extends('layouts.app')
@section('title', 'Moderate Comments')

@section('content')
<section class="section">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <h1>💬 Moderate Comments</h1>
        <a class="ghost-button" href="{{ route($routePrefix . '.dashboard') }}">Dashboard</a>
    </div>

    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    <x-search-filter 
        action="{{ route($routePrefix . '.comments.index') }}" 
        search-placeholder="Search comments by user or content..." 
        :filters="['rating' => ['5' => '5 Stars', '4' => '4 Stars', '3' => '3 Stars', '2' => '2 Stars', '1' => '1 Star']]" 
    />

    <div class="card" style="padding:0; overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $comment)
                <tr>
                    <td>
                        <strong>{{ str()->limit($comment->article?->title, 40) }}</strong>
                    </td>
                    <td>{{ $comment->user?->name ?? 'Anonymous' }}</td>
                    <td>
                        <span style="color:var(--primary-color);">{{ str_repeat('★', $comment->rating) }}{{ str_repeat('☆', 5 - $comment->rating) }}</span>
                    </td>
                    <td><small>{{ str()->limit($comment->comment, 60) }}</small></td>
                    <td><small class="muted">{{ $comment->created_at->format('M d, Y') }}</small></td>
                    <td>
                        <div class="pill-row">
                            <a class="ghost-button" href="{{ route($routePrefix . '.comments.edit', $comment) }}">Edit</a>
                            <form method="POST" action="{{ route($routePrefix . '.comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ghost-button" style="color: var(--error-text); border-color: var(--error-border);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding: 32px;" class="muted">No comments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin: 16px;">
            {{ $comments->links() }}
        </div>
    </div>
</section>
@endsection
