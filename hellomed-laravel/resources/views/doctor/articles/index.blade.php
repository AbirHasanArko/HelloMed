@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding: 0 0 16px;">
        <div>
            <h1>My articles</h1>
            <p>Create health articles and submit them for staff/admin publication approval.</p>
        </div>
        <a class="button" href="{{ route('doctor.articles.create') }}">Write new article</a>
    </div>

    <div class="card">
        @forelse ($articles as $article)
            <div class="list-item" style="margin-bottom: 12px;">
                <h3>{{ $article->title }}</h3>
                <p>
                    {{ $article->category?->name }} ·
                    Status: {{ str_replace('_', ' ', ucfirst($article->publication_status ?? 'draft')) }}
                    @if ($article->reviewer)
                        · Reviewed by {{ $article->reviewer->name }}
                    @endif
                </p>
                @if ($article->publication_status !== 'published')
                    <a class="ghost-button" href="{{ route('doctor.articles.edit', $article) }}">Edit / Resubmit</a>
                @endif
            </div>
        @empty
            <p class="muted">No articles yet.</p>
        @endforelse
        {{ $articles->links() }}
    </div>
</section>
@endsection
