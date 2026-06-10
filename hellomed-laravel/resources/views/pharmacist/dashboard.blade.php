@extends('layouts.app')

@section('content')
<section class="section">
    <h1>Pharmacy dashboard</h1>
    <div class="meta-row" style="margin-bottom:16px;">
        <a class="ghost-button" href="{{ route('pharmacist.medicines.index') }}">Manage medicines</a>
        <a class="ghost-button" href="{{ route('pharmacist.orders.index') }}">Manage orders</a>
    </div>
    <div class="grid cols-4">
        <div class="stat"><strong>{{ $medicineCount }}</strong><span class="muted">Medicines</span></div>
        <div class="stat"><strong>{{ $lowStockCount }}</strong><span class="muted">Low stock</span></div>
        <div class="stat"><strong>{{ $pendingOrders }}</strong><span class="muted">Pending orders</span></div>
        <div class="stat"><strong>{{ $processingOrders }}</strong><span class="muted">Processing orders</span></div>
    </div>
</section>
@endsection
