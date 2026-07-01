@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Create category</h1>
        <div class="card" style="max-width: 500px;">
            <form method="POST" action="{{ route($routePrefix . '.article-categories.store') }}">
                @csrf
                <label>
                    Category Name
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </label>
                
                <label>
                    Description
                    <textarea name="description">{{ old('description') }}</textarea>
                </label>

                <label>
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Active
                </label>
                
                <button class="button" type="submit" style="margin-top: 16px;">Save category</button>
            </form>
        </div>
    </section>
@endsection
