@extends('layouts.app')

@section('content')
<section class="section">
    <h1>Edit article</h1>
    <div class="card">
        <form method="POST" action="{{ route('doctor.articles.update', $article) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <label>
                Category
                <select name="article_category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($article->article_category_id === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                Title
                <input type="text" name="title" value="{{ old('title', $article->title) }}" required>
            </label>
            <label>
                Excerpt
                <textarea name="excerpt" required>{{ old('excerpt', $article->excerpt) }}</textarea>
            </label>
            <label>
                Body
                <textarea name="body" required>{{ old('body', $article->body) }}</textarea>
            </label>
            <label>
                Article image
                <input type="file" name="cover_image" accept="image/*">
            </label>
            @if ($article->cover_image_path)
                <p><img src="{{ Storage::url($article->cover_image_path) }}" alt="{{ $article->title }}" style="width: 180px; height: 110px; object-fit: cover; border-radius: 12px;"></p>
            @endif

            <div class="pill-row" style="margin-top: 14px;">
                <button class="ghost-button" type="submit" name="submit_action" value="save_draft">Save draft</button>
                <button class="button" type="submit" name="submit_action" value="submit_review">Resubmit for review</button>
            </div>
        </form>
    </div>
</section>
@endsection
