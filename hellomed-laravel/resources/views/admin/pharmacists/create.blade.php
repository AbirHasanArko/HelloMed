@extends('layouts.app')

@section('content')
    <section class="section">
        <h1>Add new pharmacist</h1>
        <div class="card">
            <form method="POST" action="{{ route('admin.pharmacists.store') }}">
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
                    Phone number
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </label>
                <label>
                    Monthly payment (BDT)
                    <input type="number" step="0.01" name="monthly_payment" value="{{ old('monthly_payment') }}">
                </label>
                <label>
                    Initial password
                    <input type="text" name="initial_password" value="{{ old('initial_password') }}" required>
                </label>
                <label>
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                    Active account
                </label>
                <button class="button" type="submit">Create pharmacist account</button>
            </form>
        </div>
    </section>
@endsection
