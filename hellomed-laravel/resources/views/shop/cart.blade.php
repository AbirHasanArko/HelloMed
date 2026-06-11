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
                    <div style="display:flex; gap:8px; align-items:flex-start;">
                        <textarea name="delivery_address" id="delivery_address" style="flex:1;" required>{{ old('delivery_address', auth()->user()->patientProfile->address ?? '') }}</textarea>
                        <button type="button" class="ghost-button" id="btn-use-location" style="white-space:nowrap; padding:10px;">📍 Use My Location</button>
                    </div>
                </label>
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                
                <script>
                    document.getElementById('btn-use-location').addEventListener('click', function() {
                        const btn = this;
                        const addressField = document.getElementById('delivery_address');
                        
                        if (!navigator.geolocation) {
                            alert('Geolocation is not supported by your browser.');
                            return;
                        }
                        
                        btn.innerText = '📍 Locating...';
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                document.getElementById('latitude').value = position.coords.latitude;
                                document.getElementById('longitude').value = position.coords.longitude;
                                
                                if (!addressField.value.trim()) {
                                    addressField.value = `Coordinates: ${position.coords.latitude}, ${position.coords.longitude}`;
                                } else if (!addressField.value.includes('(GPS Location Attached)')) {
                                    addressField.value += '\n(GPS Location Attached)';
                                }
                                
                                btn.innerText = '✅ Location Added';
                                btn.style.color = 'var(--success-text)';
                            },
                            function(error) {
                                alert('Unable to retrieve your location.');
                                btn.innerText = '📍 Use My Location';
                            }
                        );
                    });
                </script>

                <label>
                    Phone
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                </label>
                <label>
                    Payment method
                    <select name="payment_method" id="cart-payment-method-select" required>
                        <option value="cash-on-delivery">Cash on delivery</option>
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                    </select>
                </label>

                <div id="cart-mobile-payment-details" style="display: none; margin-top: 16px; padding: 16px; background: rgba(0,0,0,0.05); border-radius: 8px; border: 1px solid var(--border);">
                    <h4 style="margin-bottom: 8px;">Mobile Payment Instructions</h4>
                    <p style="font-size: 13px; margin-bottom: 16px;">
                        Please send the exact amount to the Hospital's <span id="cart-payment-provider-name">bKash/Nagad</span> number: <strong>01234567890</strong> (Personal).<br>
                        After sending the money, please enter your sender number and transaction ID below to verify your payment.
                    </p>
                    
                    <label>
                        Your Sender Number
                        <input type="text" name="sender_number" value="{{ old('sender_number') }}" placeholder="e.g. 01712345678">
                    </label>
                    <label>
                        Transaction ID
                        <input type="text" name="transaction_id" value="{{ old('transaction_id') }}" placeholder="e.g. 8M32K91L">
                    </label>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const select = document.getElementById('cart-payment-method-select');
                        const detailsDiv = document.getElementById('cart-mobile-payment-details');
                        const providerSpan = document.getElementById('cart-payment-provider-name');
                        
                        if(select && detailsDiv) {
                            select.addEventListener('change', () => {
                                if(select.value === 'bkash' || select.value === 'nagad') {
                                    detailsDiv.style.display = 'block';
                                    providerSpan.innerText = select.value === 'bkash' ? 'bKash' : 'Nagad';
                                } else {
                                    detailsDiv.style.display = 'none';
                                }
                            });
                            if(select.value === 'bkash' || select.value === 'nagad') {
                                detailsDiv.style.display = 'block';
                                providerSpan.innerText = select.value === 'bkash' ? 'bKash' : 'Nagad';
                            }
                        }
                    });
                </script>
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
