<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .totals { margin-top: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">HelloMed Pharmacy Invoice</div>
        <div>Order: {{ $order->order_number }}</div>
        <div>Patient: {{ $order->user?->name }}</div>
        <div>Address: {{ $order->delivery_address }}</div>
        <div>Phone: {{ $order->phone }}</div>
        <div>Status: {{ ucfirst($order->status) }} | Payment: {{ ucfirst($order->payment_status) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Line Total</th>
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

    <div class="totals">
        <strong>Grand Total: BDT {{ number_format((float) $order->total_amount, 2) }}</strong>
    </div>
</body>
</html>
