@extends('layouts.app')

@section('content')
<section class="section fade-in">
    <div class="card">
        <div style="display:flex;gap:14px;align-items:flex-start;">
            <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));display:grid;place-items:center;flex-shrink:0;color:white;font-weight:700;font-size:15px;">
                {{ strtoupper(substr($question->user?->name ?? '?', 0, 1)) }}
            </div>
            <div>
                <div class="tag">Q&A</div>
                <h1 style="font-size:1.6rem;">{{ $question->title }}</h1>
                <p style="font-size:15px;line-height:1.8;">{{ $question->question }}</p>
                <p class="muted" style="font-size:13px;margin-bottom:0;">Asked by {{ $question->user?->name }} · {{ ucfirst($question->status) }}</p>
            </div>
        </div>
    </div>
</section>

<section class="section fade-in">
    <div class="card">
        <h3>Answers</h3>

        @auth
            @if (in_array(auth()->user()->role, ['doctor', 'staff', 'admin'], true))
                <form method="POST" action="{{ route('qna.answers.store', $question) }}" style="margin-bottom: 20px;padding:20px;background:var(--surface-hover);border-radius:14px;">
                    @csrf
                    <label>
                        Your answer
                        <textarea name="answer" required></textarea>
                    </label>
                    <button class="button" type="submit">Submit answer</button>
                </form>
            @endif
        @endauth

        <div class="list">
            @forelse ($question->answers as $answer)
                <div class="list-item" style="display:flex;gap:14px;align-items:flex-start;">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));display:grid;place-items:center;flex-shrink:0;color:white;font-weight:700;font-size:13px;">
                        {{ strtoupper(substr($answer->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <strong>{{ $answer->user?->name }} <span class="tag" style="margin-bottom:0;padding:2px 8px;font-size:11px;">{{ ucfirst($answer->user?->role ?? 'user') }}</span></strong>
                        <p style="margin-bottom:0;">{{ $answer->answer }}</p>
                    </div>
                </div>
            @empty
                <div class="list-item muted">No answers yet.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection
