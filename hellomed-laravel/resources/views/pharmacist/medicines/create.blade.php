@extends('layouts.app')

@section('content')
<section class="section">
    <h1>Add medicine</h1>
    <div class="card" style="max-width: 600px;">
        <form method="POST" action="{{ route($routePrefix . '.medicines.store') }}" enctype="multipart/form-data">
            @csrf
            @include('pharmacist.medicines.partials.form', ['medicine' => null])
            <button class="button" type="submit">Create</button>
        </form>
    </div>
</section>
@endsection
