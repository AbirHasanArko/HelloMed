@extends('layouts.app')

@section('content')
<section class="section">
    <h1>Edit medicine</h1>
    <div class="card">
        <form method="POST" action="{{ route('pharmacist.medicines.update', $medicine) }}">
            @csrf
            @method('PUT')
            @include('pharmacist.medicines.partials.form', ['medicine' => $medicine])
            <button class="button" type="submit">Update</button>
        </form>
    </div>
</section>
@endsection
