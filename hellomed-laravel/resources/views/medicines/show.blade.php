@extends('layouts.app')

@section('content')
<section class="section">
    <div class="grid cols-2 fade-in">
        <div class="card" style="padding:0;overflow:hidden;">
            <div class="medicine-icon-placeholder" style="height:220px; overflow:hidden; position:relative;">
                @if ($medicine->image_path)
                    <img src="{{ Storage::url($medicine->image_path) }}" alt="{{ $medicine->name }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-6 9h9m-9 4.5h6"/>
                    </svg>
                @endif
            </div>
            <div style="padding:24px;">
                <div class="tag">Medicine</div>
                <h1>{{ $medicine->name }}</h1>
                <p>{{ $medicine->description }}</p>
                <div class="list" style="margin-top:12px;">
                    <div class="list-item"><strong>Group:</strong> {{ $medicine->medicine_group ?: 'N/A' }}</div>
                    <div class="list-item"><strong>Power:</strong> {{ $medicine->power ?: $medicine->strength ?: 'N/A' }}</div>
                    <div class="list-item"><strong>Amount:</strong> {{ $medicine->amount ?: 'N/A' }}</div>
                    <div class="list-item"><strong>Manufacturer:</strong> {{ $medicine->manufacturer ?: 'N/A' }}</div>
                    <div class="list-item"><strong>Requires prescription:</strong> {{ $medicine->requires_prescription ? 'Yes' : 'No' }}</div>
                </div>
            </div>
        </div>
        <div class="card fade-in fade-in-delay-1">
            <h3>Purchase</h3>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <span class="price" style="font-size:1.5rem;">BDT {{ number_format((float) $medicine->price, 2) }}</span>
                @if ($medicine->stock_quantity > 10)
                    <span class="stock-badge in-stock">In stock ({{ $medicine->stock_quantity }})</span>
                @elseif ($medicine->stock_quantity > 0)
                    <span class="stock-badge low-stock">{{ $medicine->stock_quantity }} left</span>
                @else
                    <span class="stock-badge out-of-stock">Out of stock</span>
                @endif
            </div>
            <form method="POST" action="{{ route('shop.cart.add', $medicine) }}">
                @csrf
                <label>
                    Quantity
                    <input type="number" name="quantity" min="1" max="{{ max(1, $medicine->stock_quantity) }}" value="1" required>
                </label>
                <button class="button" type="submit" style="width:100%;justify-content:center;" @disabled($medicine->stock_quantity < 1)>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Add to cart
                </button>
            </form>
            <p style="margin-top:16px;text-align:center;"><a class="ghost-button" href="{{ route('shop.cart') }}">Go to cart</a></p>
        </div>
    </div>
</section>
@endsection
