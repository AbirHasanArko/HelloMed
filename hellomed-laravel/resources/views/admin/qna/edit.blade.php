@extends('layouts.app')
@section('title', 'Manage Question')

@section('content')
<section class="section">
    <div style="margin-bottom: 24px;">
        <h1 style="margin-bottom: 4px;">{{ $question->title }}</h1>
        <p class="muted">Asked by {{ $question->user?->name ?? 'Anonymous' }} on {{ $question->created_at->format('M d, Y') }}</p>
    </div>

    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="error-box" style="margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid cols-2 fade-in">
        <div class="card">
            <h3>Question Details</h3>
            <p style="white-space: pre-wrap; font-size: 15px; background: rgba(0,0,0,0.02); padding: 16px; border-radius: 8px; border: 1px solid var(--border);">{{ $question->question }}</p>
            
            <hr style="margin: 24px 0; border:0; border-top:1px solid var(--border);">
            
            <h3>Answers ({{ $question->answers->count() }})</h3>
            @forelse($question->answers as $ans)
                <div style="margin-bottom: 16px; padding: 16px; background: {{ $ans->is_official ? 'rgba(14, 165, 233, 0.05)' : 'rgba(0,0,0,0.02)' }}; border: 1px solid {{ $ans->is_official ? 'var(--primary-color)' : 'var(--border)' }}; border-radius: 8px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom: 8px;">
                        <strong>{{ $ans->user?->name ?? 'Anonymous' }} @if($ans->is_official) <span class="tag" style="background:var(--primary-color);color:#fff;font-size:10px;padding:2px 6px;">Official</span> @endif</strong>
                        <small class="muted">{{ $ans->created_at->diffForHumans() }}</small>
                    </div>
                    <p style="margin:0; white-space: pre-wrap; font-size:14px;">{{ $ans->answer }}</p>
                </div>
            @empty
                <p class="muted">No answers yet.</p>
            @endforelse
        </div>

        <div class="card">
            <h3>Update & Answer</h3>
            <form method="POST" action="{{ route($routePrefix . '.qna.update', $question) }}">
                @csrf
                @method('PUT')
                
                <label>
                    Status
                    <select name="status">
                        <option value="open" @selected($question->status === 'open')>Open</option>
                        <option value="resolved" @selected($question->status === 'resolved')>Resolved</option>
                        <option value="closed" @selected($question->status === 'closed')>Closed</option>
                    </select>
                </label>
                
                <label>
                    Add Official Answer (Optional)
                    <textarea name="new_answer" rows="6" placeholder="Type a helpful answer..."></textarea>
                </label>

                <div style="margin-top: 24px; display: flex; gap: 12px;">
                    <button class="button" type="submit">Update & Submit Answer</button>
                    <a class="ghost-button" href="{{ route($routePrefix . '.qna.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
