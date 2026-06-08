@extends('layouts.app')

@section('content')
<section class="section">
    <div class="card">
        <div class="tag">Medicine order</div>
        <h1>{{ $order->order_number }}</h1>
        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
        <p><strong>Payment:</strong> {{ ucfirst($order->payment_status) }} via {{ $order->payment_method }}</p>
        <p><strong>Delivery address:</strong> {{ $order->delivery_address }}</p>
        <p><strong>Phone:</strong> {{ $order->phone }}</p>
        @if ($order->contains_prescription_items)
            <p><strong>Prescription:</strong> Submitted</p>
        @endif

        <div class="pill-row" style="margin-top:12px;">
            <a class="ghost-button" href="{{ route('patient.medicine-orders.invoice', $order) }}">Download invoice (PDF)</a>

            @if (in_array($order->payment_method, ['bkash', 'nagad'], true) && $order->payment_status !== 'paid')
                <a class="button" href="{{ route('shop.payments.start', ['order' => $order, 'provider' => $order->payment_method]) }}">Pay now via {{ strtoupper($order->payment_method) }}</a>
            @endif
        </div>
    </div>

    <div class="card" style="margin-top:20px;">
        <h3>Items</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->medicine?->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>BDT {{ number_format((float) $item->unit_price, 2) }}</td>
                        <td>BDT {{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p style="margin-top:10px;"><strong>Grand total:</strong> BDT {{ number_format((float) $order->total_amount, 2) }}</p>
    </div>
</section>
@endsection
