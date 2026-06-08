@extends('layouts.app')

@section('content')
<section class="section">
    <div class="card">
        <div class="tag">Mock payment gateway</div>
        <h1>{{ strtoupper($provider) }} payment</h1>
        <p>Order: <strong>{{ $order->order_number }}</strong></p>
        <p>Amount: <strong>BDT {{ number_format((float) $order->total_amount, 2) }}</strong></p>
        <div class="pill-row" style="margin-top:16px;">
            <a class="button" href="{{ route('shop.payments.callback', ['order' => $order, 'provider' => $provider, 'status' => 'success', 'token' => $order->payment_callback_token]) }}">Simulate success</a>
            <a class="ghost-button" href="{{ route('shop.payments.callback', ['order' => $order, 'provider' => $provider, 'status' => 'failed', 'token' => $order->payment_callback_token]) }}">Simulate failed</a>
        </div>
    </div>
</section>
@endsection
