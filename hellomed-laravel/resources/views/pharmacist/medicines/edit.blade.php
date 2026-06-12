@extends('layouts.app')

@section('content')
<section class="section">
    <h1>Edit medicine</h1>
    <div class="card" style="max-width: 600px;">
        <form method="POST" action="{{ route($routePrefix . '.medicines.update', $medicine) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('pharmacist.medicines.partials.form', ['medicine' => $medicine])
            <button class="button" type="submit">Update</button>
        </form>
    </div>
</section>
@endsection
