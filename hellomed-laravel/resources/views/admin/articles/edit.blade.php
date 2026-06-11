@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Edit article</h1>
        <div class="card">
            <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data">
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
                    <input type="text" name="title" value="{{ $article->title }}" required>
                </label>
                <label>
                    Excerpt
                    <textarea name="excerpt" required>{{ $article->excerpt }}</textarea>
                </label>
                <label>
                    Body
                    <textarea name="body" required>{{ $article->body }}</textarea>
                </label>
                <label>
                    Article image
                    <input type="file" name="cover_image" accept="image/*">
                </label>
                @if ($article->cover_image_path)
                    <p><img src="{{ Storage::url($article->cover_image_path) }}" alt="{{ $article->title }}" style="width: 180px; height: 110px; object-fit: cover; border-radius: 12px;"></p>
                @endif
                <div style="margin: 20px 0; padding: 15px; border: 1px solid var(--border); border-radius: 8px;">
                    <h4 style="margin-top: 0;">Home Page Featured Settings</h4>
                    <label style="margin-bottom: 10px;">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $article->is_featured))>
                        Show on Home Page
                    </label>
                    <label style="margin-bottom: 0;">
                        Display Order (e.g. 1, 2, 3)
                        <input type="number" name="featured_order" value="{{ old('featured_order', $article->featured_order) }}" min="0">
                    </label>
                </div>
                <label>
                    <input type="checkbox" name="is_published" value="1" @checked($article->is_published)> Published
                </label>
                <button class="button" type="submit">Update article</button>
            </form>
        </div>
    </section>
@endsection
