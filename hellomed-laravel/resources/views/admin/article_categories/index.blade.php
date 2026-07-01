@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="nav-inner" style="padding: 0 0 16px;">
            <div>
                <h1>Article Categories</h1>
                <p>Manage the categories for hospital articles and health content.</p>
            </div>
            <a class="button" href="{{ route($routePrefix . '.article-categories.create') }}">New category</a>
        </div>

        <div class="card">
            @foreach ($categories as $category)
                <div class="list-item" style="margin-bottom: 12px;">
                    <h3>{{ $category->name }}</h3>
                    <p>
                        Status: {{ $category->is_active ? 'Active' : 'Inactive' }} ·
                        Articles: {{ $category->articles_count }}
                    </p>
                    <p style="color: var(--text-secondary); font-size: 14px; margin-top: 4px;">
                        {{ $category->description ?: 'No description provided.' }}
                    </p>
                    <div class="pill-row">
                        <a class="ghost-button" href="{{ route($routePrefix . '.article-categories.edit', $category) }}">Edit</a>
                        @if ($category->articles_count === 0)
                            <form method="POST" action="{{ route($routePrefix . '.article-categories.destroy', $category) }}" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ghost-button" style="color: var(--error-text); border-color: var(--error-border);">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
            {{ $categories->links() }}
        </div>
    </section>
@endsection
