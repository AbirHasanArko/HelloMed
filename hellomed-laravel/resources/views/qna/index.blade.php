@extends('layouts.app')
@section('title', 'Q&A')

@section('content')
<section class="section">
    <div class="nav-inner fade-in" style="padding: 0 0 20px;">
        <div>
            <h1>Q&A</h1>
            <p>Patients can ask clinical questions and doctors/staff provide official answers.</p>
        </div>
    </div>

    @auth
        @if (auth()->user()->role === 'patient')
            <div class="card fade-in" style="margin-bottom: 20px;">
                <h3>Ask a question</h3>
                <form method="POST" action="{{ route('qna.store') }}">
                    @csrf
                    <label>
                        Title
                        <input type="text" name="title" required>
                    </label>
                    <label>
                        Question details
                        <textarea name="question" required></textarea>
                    </label>
                    <button class="button" type="submit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        Post question
                    </button>
                </form>
            </div>
        @endif
    @endauth

    <div class="card fade-in">
        <div class="list">
            @forelse ($questions as $question)
                <a class="list-item" href="{{ route('qna.show', $question) }}" style="display:flex;gap:14px;align-items:flex-start;">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));display:grid;place-items:center;flex-shrink:0;color:white;font-weight:700;font-size:13px;">
                        {{ strtoupper(substr($question->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div style="flex:1;">
                        <strong>{{ $question->title }}</strong>
                        <p style="margin-bottom:6px;">{{ \Illuminate\Support\Str::limit($question->question, 180) }}</p>
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            <span class="muted" style="font-size:12px;">Asked by {{ $question->user?->name }}</span>
                            <span class="tag" style="margin-bottom:0;padding:2px 8px;font-size:11px;">{{ ucfirst($question->status) }}</span>
                            <span class="question-count-badge">{{ $question->answers->count() }} answers</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="list-item muted">No questions posted yet.</div>
            @endforelse
        </div>
        <div style="margin-top: 20px;">{{ $questions->links() }}</div>
    </div>
</section>
@endsection
