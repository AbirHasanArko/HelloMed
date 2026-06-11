@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding:0 0 16px;">
        <div>
            <h1>Manage medicines</h1>
            <p>Maintain medicine catalog, pricing, and stock for patient purchases.</p>
        </div>
        <a class="button" href="{{ route($routePrefix . '.medicines.create') }}">Add medicine</a>
    </div>

    <x-search-filter 
        action="{{ route($routePrefix . '.medicines.index') }}" 
        search-placeholder="Search medicines by name, manufacturer, group..." 
        :filters="['is_active' => ['1' => 'Active', '0' => 'Inactive'], 'requires_prescription' => ['1' => 'Prescription Only', '0' => 'OTC']]" 
    />

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
                        <td>
                            <div class="pill-row">
                                <a class="ghost-button" href="{{ route($routePrefix . '.medicines.edit', $medicine) }}">Edit</a>
                                <form action="{{ route($routePrefix . '.medicines.destroy', $medicine) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this medicine?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ghost-button" style="color: var(--error-text); border-color: var(--error-border);">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:20px;">{{ $medicines->links() }}</div>
    </div>
</section>
@endsection
