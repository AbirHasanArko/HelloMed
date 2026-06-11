@extends('layouts.app')
@section('title', 'Edit Comment')

@section('content')
<section class="section">
    <div style="margin-bottom: 24px;">
        <h1 style="margin-bottom: 4px;">Edit Comment</h1>
        <p class="muted">Moderate comment on article: <strong>{{ $comment->article?->title }}</strong></p>
    </div>

    @if ($errors->any())
        <div class="error-box" style="margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="max-width: 600px;">
        <form method="POST" action="{{ route($routePrefix . '.comments.update', $comment) }}">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 16px;">
                <strong>Author:</strong> {{ $comment->user?->name ?? 'Anonymous' }}<br>
                <strong>Date:</strong> {{ $comment->created_at->format('M d, Y h:i A') }}
            </div>

            <label>
                Rating (1-5)
                <input type="number" name="rating" min="1" max="5" value="{{ old('rating', $comment->rating) }}" required>
            </label>
            
            <label>
                Comment Text
                <textarea name="comment" rows="6" required>{{ old('comment', $comment->comment) }}</textarea>
            </label>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button class="button" type="submit">Update Comment</button>
                <a class="ghost-button" href="{{ route($routePrefix . '.comments.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
