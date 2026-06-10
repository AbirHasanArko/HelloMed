@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Add new staff</h1>
        <div class="card">
            <form method="POST" action="{{ route('admin.staff.store') }}">
                @csrf
                <label>
                    Name
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </label>
                <label>
                    Email
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </label>
                <label>
                    Initial password
                    <input type="text" name="initial_password" value="{{ old('initial_password') }}" required>
                </label>
                <label>
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                    Active account
                </label>
                <button class="button" type="submit">Create staff account</button>
            </form>
        </div>
    </section>
@endsection
