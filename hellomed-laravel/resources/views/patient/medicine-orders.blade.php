@extends('layouts.app')

@section('content')
<section class="section">
    <h1>My medicine orders</h1>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Order no</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->items_count }}</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>{{ ucfirst($order->payment_status) }}</td>
                        <td>BDT {{ number_format((float) $order->total_amount, 2) }}</td>
                        <td><a class="ghost-button" href="{{ route('patient.medicine-orders.show', $order) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted">No medicine orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:20px;">{{ $orders->links() }}</div>
    </div>
</section>
@endsection
