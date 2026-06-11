@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding: 0 0 16px;">
        <div>
            <h1>Medicine orders</h1>
            <p>Manage online orders and record in-store POS sales.</p>
        </div>
        <a class="button" href="{{ route('pharmacist.orders.create') }}">New Offline Sale</a>
    </div>

    <x-search-filter 
        action="{{ route('pharmacist.orders.index') }}" 
        search-placeholder="Search orders by number, patient name, email..." 
        :filters="['status' => ['pending' => 'Pending', 'processing' => 'Processing', 'completed' => 'Completed', 'cancelled' => 'Cancelled']]" 
    />

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
                        <td>
                            <strong>{{ $order->user?->name ?? $order->customer_name }}</strong>
                            <div style="font-size: 11px; margin-top: 4px;">
                                <a href="{{ route('pharmacist.orders.invoice', $order) }}" target="_blank" class="muted">Download Receipt</a>
                            </div>
                            @if($order->phone)
                                <div class="muted" style="font-size: 13px; margin-top: 2px;">{{ $order->phone }}</div>
                            @endif
                            @if($order->latitude && $order->longitude)
                                <div style="margin-top: 4px; font-size: 12px;">
                                    <a href="https://maps.google.com/?q={{ $order->latitude }},{{ $order->longitude }}" target="_blank" style="color:var(--primary); text-decoration:underline;">📍 View on Map</a>
                                </div>
                            @endif
                        </td>
                        <td>BDT {{ number_format((float) $order->total_amount, 2) }}</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>
                            {{ ucfirst($order->payment_status) }}
                            @if(in_array($order->payment_method, ['bkash', 'nagad']))
                                <div class="muted" style="font-size:12px; margin-top:4px;">
                                    <strong>{{ strtoupper($order->payment_method) }}</strong><br>
                                    Trx: {{ $order->transaction_id ?? 'N/A' }}<br>
                                    From: {{ $order->sender_number ?? 'N/A' }}
                                </div>
                            @endif
                        </td>
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
                            <form method="POST" action="{{ route('pharmacist.orders.update', $order) }}" style="display:flex; flex-direction:column; gap:8px;" autocomplete="off">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <div style="font-size: 11px; color: var(--muted); margin-bottom: 2px;">Status</div>
                                    <select name="status" style="width: 100%;">
                                        @foreach (['pending','processing','completed','cancelled'] as $status)
                                            <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: var(--muted); margin-bottom: 2px;">Payment</div>
                                    <select name="payment_status" style="width: 100%;">
                                        @foreach (['pending','paid','failed','refunded'] as $status)
                                            <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="button" type="submit" style="margin-top: 4px;">Save</button>
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
