@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="nav-inner" style="padding: 0 0 16px;">
            <div>
                <h1>Articles</h1>
                <p>Manage hospital articles and general health content.</p>
            </div>
            <a class="button" href="{{ route('admin.articles.create') }}">New article</a>
        </div>
        <div class="card">
            @foreach ($articles as $article)
                <div class="list-item" style="margin-bottom: 12px;">
                    <h3>{{ $article->title }}</h3>
                    <p>
                        {{ $article->category?->name }} ·
                        Author: {{ $article->author?->name ?? 'N/A' }} ·
                        Status: {{ str_replace('_', ' ', ucfirst($article->publication_status ?? 'draft')) }}
                    </p>
                    <div class="pill-row">
                        <a class="ghost-button" href="{{ route('admin.articles.edit', $article) }}">Edit</a>

                        @if (($article->publication_status ?? 'draft') === 'pending_review')
                            <form method="POST" action="{{ route('admin.articles.review', $article) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="decision" value="approve">
                                <button class="button" type="submit">Approve & publish</button>
                            </form>
                            <form method="POST" action="{{ route('admin.articles.review', $article) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="decision" value="reject">
                                <button class="ghost-button" type="submit">Reject</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
            {{ $articles->links() }}
        </div>
    </section>
@endsection
