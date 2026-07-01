@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Edit category</h1>
        <div class="card" style="max-width: 500px;">
            <form method="POST" action="{{ route($routePrefix . '.article-categories.update', $articleCategory) }}">
                @csrf
                @method('PUT')
                <label>
                    Category Name
                    <input type="text" name="name" value="{{ old('name', $articleCategory->name) }}" required>
                </label>
                
                <label>
                    Description
                    <textarea name="description">{{ old('description', $articleCategory->description) }}</textarea>
                </label>

                <label>
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $articleCategory->is_active))> Active
                </label>
                
                <button class="button" type="submit" style="margin-top: 16px;">Update category</button>
            </form>
        </div>
    </section>
@endsection
