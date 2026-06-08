@extends('layouts.app')

@section('content')
    <section class="section fade-in">
        <div class="card" style="padding:0;overflow:hidden;">
            @if ($article->cover_image_path)
                <div style="position:relative;width:100%;height:300px;overflow:hidden;">
                    <img src="{{ Storage::url($article->cover_image_path) }}" alt="{{ $article->title }}" style="width:100%;height:100%;object-fit:cover;">
                    <div style="position:absolute;inset:0;background:var(--overlay-gradient);"></div>
                    <div style="position:absolute;bottom:24px;left:28px;right:28px;">
                        <span class="tag" style="background:rgba(255,255,255,0.2);color:white;">{{ $article->category?->name }}</span>
                        <h1 style="color:white;-webkit-text-fill-color:white;background:none;margin-bottom:8px;">{{ $article->title }}</h1>
                        <div style="color:rgba(255,255,255,0.8);font-size:13px;">
                            {{ $article->published_at?->format('M d, Y') }} ·
                            Writer doctor: {{ $article->author?->doctorProfile?->name ?? $article->author?->name ?? 'HelloMed Team' }}
                        </div>
                    </div>
                </div>
            @else
                <div style="padding:28px;">
                    <div class="tag">{{ $article->category?->name }}</div>
                    <h1>{{ $article->title }}</h1>
                    <div class="muted" style="font-size:13px;">
                        {{ $article->published_at?->format('M d, Y') }} ·
                        Writer doctor: {{ $article->author?->doctorProfile?->name ?? $article->author?->name ?? 'HelloMed Team' }}
                    </div>
                </div>
            @endif
            <div style="padding:28px;">
                <p style="font-size:1.05rem;line-height:1.8;color:var(--text-secondary);">{{ $article->excerpt }}</p>
                <p><strong>Average reader rating:</strong> {{ $averageRating > 0 ? $averageRating.'/5' : 'No ratings yet' }}</p>
            </div>
        </div>
    </section>

    <section class="section fade-in">
        <div class="card">
            <div style="font-size:15px;line-height:1.9;color:var(--text);">
                {!! $article->body !!}
            </div>
        </div>
    </section>

    <section class="section fade-in">
        <div class="card">
            <h3>Reader comments</h3>
            @auth
                @if (auth()->user()->role === 'patient')
                    <form method="POST" action="{{ route('articles.comments.store', $article) }}" style="margin-bottom: 20px; padding: 20px; background: var(--surface-hover); border-radius: 14px;">
                        @csrf
                        <label>
                            Rating (optional)
                            <select name="rating">
                                <option value="">No rating</option>
                                @foreach ([5,4,3,2,1] as $score)
                                    <option value="{{ $score }}">{{ $score }} / 5</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            Comment
                            <textarea name="comment" required></textarea>
                        </label>
                        <button class="button" type="submit">Post comment</button>
                    </form>
                @endif
            @endauth

            <div class="list">
                @forelse ($article->comments as $comment)
                    <div class="list-item" style="display:flex;gap:14px;align-items:flex-start;">
                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg, var(--gradient-start), var(--gradient-end));display:grid;place-items:center;flex-shrink:0;color:white;font-weight:700;font-size:13px;">
                            {{ strtoupper(substr($comment->user?->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <strong>{{ $comment->user?->name }}{{ $comment->rating ? ' · '.$comment->rating.'/5' : '' }}</strong>
                            <p style="margin-bottom:0;">{{ $comment->comment }}</p>
                        </div>
                    </div>
                @empty
                    <div class="list-item muted">No comments yet.</div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
