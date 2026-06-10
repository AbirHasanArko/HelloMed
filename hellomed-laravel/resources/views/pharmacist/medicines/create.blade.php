@extends('layouts.app')

@section('content')
<section class="section">
    <h1>Add medicine</h1>
    <div class="card">
        <form method="POST" action="{{ route('pharmacist.medicines.store') }}">
            @csrf
            @include('pharmacist.medicines.partials.form', ['medicine' => null])
            <button class="button" type="submit">Create</button>
        </form>
    </div>
</section>
@endsection
