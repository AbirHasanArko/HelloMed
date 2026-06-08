@extends('layouts.app')

@section('content')
<section class="section fade-in">
    <h1>Medicine cart</h1>
    <p>Review your medicines, update quantity, and place the order.</p>

    <div class="card" style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Unit price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td><strong>{{ $item['medicine']->name }}</strong></td>
                        <td>BDT {{ number_format((float) $item['medicine']->price, 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('shop.cart.update', $item['medicine']) }}" style="display:flex;gap:6px;align-items:center;">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" min="1" max="{{ $item['medicine']->stock_quantity }}" value="{{ $item['quantity'] }}" style="width:70px;">
                                <button class="ghost-button" type="submit" style="padding:6px 10px;font-size:12px;">Update</button>
                            </form>
                        </td>
                        <td><span class="price">BDT {{ number_format((float) $item['line_total'], 2) }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('shop.cart.remove', $item['medicine']) }}">
                                @csrf
                                @method('DELETE')
                                <button class="ghost-button" type="submit" style="padding:6px 10px;font-size:12px;color:var(--error-text);">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted" style="text-align:center;padding:24px;">Your cart is empty.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card fade-in" style="margin-top:24px;">
        <h3>Checkout</h3>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <span style="font-size:13px;color:var(--muted);">Grand total:</span>
            <span class="price" style="font-size:1.5rem;">BDT {{ number_format((float) $total, 2) }}</span>
        </div>
        <p class="muted" style="font-size:13px;">If any medicine requires prescription, upload a valid file before placing the order.</p>
        @auth
            <form method="POST" action="{{ route('shop.checkout') }}" enctype="multipart/form-data">
                @csrf
                <label>
                    Delivery address
                    <textarea name="delivery_address" required>{{ old('delivery_address') }}</textarea>
                </label>
                <label>
                    Phone
                    <input type="text" name="phone" value="{{ old('phone') }}" required>
                </label>
                <label>
                    Payment method
                    <select name="payment_method" required>
                        <option value="cash-on-delivery">Cash on delivery</option>
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                    </select>
                </label>
                <label>
                    Notes
                    <textarea name="notes">{{ old('notes') }}</textarea>
                </label>
                <label>
                    Prescription file (required for Rx medicines)
                    <input type="file" name="prescription" accept=".jpg,.jpeg,.png,.pdf">
                </label>
                <button class="button" type="submit" style="width:100%;justify-content:center;" @disabled(empty($items))>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>
                    Place order
                </button>
            </form>
        @else
            <p>Please <a href="{{ route('login') }}">login</a> to checkout.</p>
        @endauth
    </div>
</section>
@endsection
