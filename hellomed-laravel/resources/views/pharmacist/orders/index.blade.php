@extends('layouts.app')

@section('content')
<section class="section">
    <h1>Medicine orders</h1>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Order no</th>
                    <th>Patient</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Prescription</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->user?->name }}</td>
                        <td>BDT {{ number_format((float) $order->total_amount, 2) }}</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>{{ ucfirst($order->payment_status) }}</td>
                        <td>
                            @if ($order->prescription_path)
                                <a href="{{ route('pharmacist.orders.prescription', $order) }}" target="_blank">View file</a>
                            @elseif ($order->contains_prescription_items)
                                <span class="muted">Missing</span>
                            @else
                                <span class="muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('pharmacist.orders.update', $order) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status">
                                    @foreach (['pending','processing','completed','cancelled'] as $status)
                                        <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <select name="payment_status">
                                    @foreach (['pending','paid','failed','refunded'] as $status)
                                        <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <button class="button" type="submit">Save</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:20px;">{{ $orders->links() }}</div>
    </div>
</section>
@endsection
