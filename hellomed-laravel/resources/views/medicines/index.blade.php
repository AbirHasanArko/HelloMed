@extends('layouts.app')
@section('title', 'Medicine Shop')

@section('content')
<section class="section">
    <div class="nav-inner fade-in" style="padding:0 0 20px;">
        <div>
            <h1>Medicine shop</h1>
            <p>Order pharmacy items online and receive delivery updates from the hospital pharmacy team.</p>
        </div>
        <a class="button" href="{{ route('shop.cart') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            View cart
        </a>
    </div>
    <div class="grid cols-4">
        @foreach ($medicines as $medicine)
            <div class="card photo-card fade-in">
                <div class="medicine-icon-placeholder" style="overflow: hidden; position: relative;">
                    @if ($medicine->image_path)
                        <img src="{{ Storage::url($medicine->image_path) }}" alt="{{ $medicine->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="photo-card-overlay"></div>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-6 9h9m-9 4.5h6"/>
                        </svg>
                    @endif
                    <span class="photo-card-badge tag" style="margin-bottom:0;">{{ $medicine->power ?: $medicine->strength ?: 'General use' }}</span>
                </div>
                <div class="photo-card-body">
                    <h3>{{ $medicine->name }}</h3>
                    <p style="margin-bottom:6px;"><strong>Group:</strong> {{ $medicine->medicine_group ?: 'N/A' }}</p>
                    <p style="margin-bottom:6px;"><strong>Amount:</strong> {{ $medicine->amount ?: 'N/A' }}</p>
                    <p style="margin-bottom:6px;">{{ $medicine->manufacturer }}</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <span class="price">BDT {{ number_format((float) $medicine->price, 2) }}</span>
                        @if ($medicine->stock_quantity > 10)
                            <span class="stock-badge in-stock">In stock</span>
                        @elseif ($medicine->stock_quantity > 0)
                            <span class="stock-badge low-stock">{{ $medicine->stock_quantity }} left</span>
                        @else
                            <span class="stock-badge out-of-stock">Out of stock</span>
                        @endif
                    </div>
                    <div class="pill-row">
                        <a class="ghost-button" href="{{ route('medicines.show', $medicine) }}">Details</a>
                        <form method="POST" action="{{ route('shop.cart.add', $medicine) }}">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button class="button" type="submit" @disabled($medicine->stock_quantity < 1)>Add</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div style="margin-top:24px;">{{ $medicines->links() }}</div>
</section>
@endsection
