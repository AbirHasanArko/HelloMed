@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding:0 0 16px;">
        <div>
            <h1>Manage medicines</h1>
            <p>Maintain medicine catalog, pricing, and stock for patient purchases.</p>
        </div>
        <a class="button" href="{{ route('pharmacist.medicines.create') }}">Add medicine</a>
    </div>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Group</th>
                    <th>Power</th>
                    <th>Amount</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicines as $medicine)
                    <tr>
                        <td>{{ $medicine->name }}</td>
                        <td>{{ $medicine->medicine_group ?: 'N/A' }}</td>
                        <td>{{ $medicine->power ?: $medicine->strength ?: 'N/A' }}</td>
                        <td>{{ $medicine->amount ?: 'N/A' }}</td>
                        <td>BDT {{ number_format((float) $medicine->price, 2) }}</td>
                        <td>{{ $medicine->stock_quantity }}</td>
                        <td>{{ $medicine->is_active ? 'Yes' : 'No' }}</td>
                        <td><a class="ghost-button" href="{{ route('pharmacist.medicines.edit', $medicine) }}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:20px;">{{ $medicines->links() }}</div>
    </div>
</section>
@endsection
