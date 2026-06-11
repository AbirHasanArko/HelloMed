@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Edit pharmacist: {{ $user->name }}</h1>
        <div class="card">
            <form method="POST" action="{{ route('admin.pharmacists.update', $user) }}">
                @csrf
                @method('PUT')
                <label>
                    Name
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </label>
                <label>
                    Email
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </label>
                <label>
                    Phone number
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                </label>
                <label>
                    Monthly payment (BDT)
                    <input type="number" step="0.01" name="monthly_payment" value="{{ old('monthly_payment', $user->monthly_payment) }}">
                </label>
                <label>
                    New password <span class="muted">(leave blank to keep current)</span>
                    <input type="text" name="password" value="{{ old('password') }}">
                </label>
                <label>
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))>
                    Active account
                </label>
                <button class="button" type="submit">Update pharmacist account</button>
            </form>
        </div>
    </section>
@endsection
