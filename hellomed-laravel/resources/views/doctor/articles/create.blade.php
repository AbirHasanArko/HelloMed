@extends('layouts.app')

@section('content')
<section class="section">
    <h1>Write article</h1>
    <div class="card">
        <form method="POST" action="{{ route('doctor.articles.store') }}" enctype="multipart/form-data">
            @csrf
            <label>
                Category
                <select name="article_category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('article_category_id') === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                Title
                <input type="text" name="title" value="{{ old('title') }}" required>
            </label>
            <label>
                Excerpt
                <textarea name="excerpt" required>{{ old('excerpt') }}</textarea>
            </label>
            <label>
                Body
                <textarea name="body" required>{{ old('body') }}</textarea>
            </label>
            <label>
                Article image
                <input type="file" name="cover_image" accept="image/*">
            </label>

            <div class="pill-row" style="margin-top: 14px;">
                <button class="ghost-button" type="submit" name="submit_action" value="save_draft">Save draft</button>
                <button class="button" type="submit" name="submit_action" value="submit_review">Submit for review</button>
            </div>
        </form>
    </div>
</section>
@endsection
