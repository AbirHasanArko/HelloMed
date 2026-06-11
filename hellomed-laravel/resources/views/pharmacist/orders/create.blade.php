@extends('layouts.app')

@section('content')
<section class="section">
    <div class="nav-inner" style="padding: 0 0 16px;">
        <div>
            <h1>Offline Medicine Sale</h1>
            <p>Record an in-store POS sale, auto-fill patient details, and instantly adjust stock.</p>
        </div>
        <a class="ghost-button" href="{{ route('pharmacist.orders.index') }}">Back to Orders</a>
    </div>

    @if ($errors->any())
        <div class="card" style="margin-bottom: 16px; background: rgba(var(--error-rgb, 220, 38, 38), 0.1); border-color: var(--error-border); color: var(--error-text);">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pharmacist.orders.store') }}" method="POST" id="pos-form">
        @csrf
        <div class="grid cols-2" style="gap: 24px; align-items: start;">
            
            <!-- Customer Info Panel -->
            <div class="card" style="display: flex; flex-direction: column; gap: 16px;">
                <h3 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 12px;">Customer Details</h3>
                
                <!-- Hidden user_id -->
                <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}">

                <!-- Phone / Email Search -->
                <div style="position: relative;" class="autocomplete-wrapper" id="patient-search-wrapper">
                    <label style="display:block; margin-bottom:8px; font-weight:600;">Search Existing Patient (Phone or Email)</label>
                    <input type="text" id="patient_search" class="input" placeholder="Type phone number or email..." autocomplete="off">
                    <div class="autocomplete-dropdown" id="patient-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--surface-raised); border: 1px solid var(--border); border-radius: 8px; margin-top: 4px; box-shadow: var(--shadow-lg); z-index: 50; max-height: 250px; overflow-y: auto;">
                        <div class="autocomplete-results"></div>
                    </div>
                    <small class="muted" style="display:block; margin-top:4px;">Selecting a patient links the order to their account.</small>
                </div>

                <div class="grid cols-2">
                    <label>
                        Customer Name <span style="color:var(--error-text);">*</span>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required>
                    </label>
                    <label>
                        Phone Number <span style="color:var(--error-text);">*</span>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required>
                    </label>
                </div>
                
                <label>
                    Email Address (Optional)
                    <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}">
                </label>

                <div style="margin-top: 8px;">
                    <label>Payment Method</label>
                    <select name="payment_method" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; background: var(--input-bg); color: var(--text);">
                        <option value="cash" @selected(old('payment_method') === 'cash')>Cash</option>
                        <option value="card" @selected(old('payment_method') === 'card')>Credit/Debit Card</option>
                        <option value="mobile_banking" @selected(old('payment_method') === 'mobile_banking')>Mobile Banking (bKash/Nagad)</option>
                    </select>
                </div>
            </div>

            <!-- Cart Panel -->
            <div class="card" style="display: flex; flex-direction: column; gap: 16px;">
                <h3 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 12px;">Order Items</h3>
                
                <!-- Medicine Search -->
                <div style="position: relative;" class="autocomplete-wrapper" id="medicine-search-wrapper">
                    <label style="display:block; margin-bottom:8px; font-weight:600;">Add Medicine</label>
                    <input type="text" id="medicine_search" class="input" placeholder="Search medicine by name or generic name..." autocomplete="off">
                    <div class="autocomplete-dropdown" id="medicine-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--surface-raised); border: 1px solid var(--border); border-radius: 8px; margin-top: 4px; box-shadow: var(--shadow-lg); z-index: 50; max-height: 250px; overflow-y: auto;">
                        <div class="autocomplete-results"></div>
                    </div>
                </div>

                <div style="border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                    <table class="table" style="margin: 0;">
                        <thead>
                            <tr>
                                <th>Medicine</th>
                                <th style="width: 80px;">Qty</th>
                                <th>Price</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body">
                            <!-- Items injected via JS -->
                            <tr id="empty-cart-row">
                                <td colspan="4" class="muted" style="text-align:center; padding:20px;">No items added yet.</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align: right;">Total:</th>
                                <th colspan="2" style="font-size: 1.2em; color: var(--primary-color);">BDT <span id="cart-total">0.00</span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div style="margin-top: auto; padding-top: 16px;">
                    <button type="submit" class="button" style="width: 100%; padding: 14px; font-size: 1.1em;" id="submit-btn" disabled>Complete Sale</button>
                </div>
            </div>

        </div>
    </form>
</section>

<style>
    .input { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; background: var(--input-bg); color: var(--text); }
    .autocomplete-item:hover { background: var(--surface-hover); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Patient Search
    const patientInput = document.getElementById('patient_search');
    const patientDropdown = document.getElementById('patient-dropdown');
    const patientResults = patientDropdown.querySelector('.autocomplete-results');
    let patientTimer;

    patientInput.addEventListener('input', function() {
        clearTimeout(patientTimer);
        const query = this.value.trim();
        
        if (query.length < 2) {
            patientDropdown.style.display = 'none';
            return;
        }

        patientTimer = setTimeout(() => {
            fetch(`{{ route('pharmacist.api.patients') }}?query=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                patientResults.innerHTML = '';
                if (data.length === 0) {
                    patientResults.innerHTML = '<div style="padding: 12px 16px; color: var(--muted);">No matching patients found.</div>';
                } else {
                    data.forEach(user => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.style.cssText = 'padding: 12px 16px; cursor: pointer; border-bottom: 1px solid var(--border-light);';
                        div.innerHTML = `<strong>${user.name}</strong> <div style="font-size: 12px; color: var(--muted); margin-top:4px;">${user.phone} | ${user.email}</div>`;
                        
                        div.addEventListener('click', () => {
                            document.getElementById('user_id').value = user.id;
                            document.getElementById('customer_name').value = user.name;
                            document.getElementById('phone').value = user.phone;
                            document.getElementById('customer_email').value = user.email || '';
                            patientInput.value = '';
                            patientDropdown.style.display = 'none';
                            // Make fields read-only if linked? Optional.
                        });
                        patientResults.appendChild(div);
                    });
                }
                patientDropdown.style.display = 'block';
            });
        }, 300);
    });

    // Medicine Search and Cart
    const medicineInput = document.getElementById('medicine_search');
    const medicineDropdown = document.getElementById('medicine-dropdown');
    const medicineResults = medicineDropdown.querySelector('.autocomplete-results');
    const cartBody = document.getElementById('cart-body');
    const cartTotalEl = document.getElementById('cart-total');
    const submitBtn = document.getElementById('submit-btn');
    let medicineTimer;
    let cart = [];

    function updateCartUI() {
        const emptyRow = document.getElementById('empty-cart-row');
        cartBody.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
            if(emptyRow) cartBody.appendChild(emptyRow);
            else cartBody.innerHTML = '<tr id="empty-cart-row"><td colspan="4" class="muted" style="text-align:center; padding:20px;">No items added yet.</td></tr>';
            cartTotalEl.innerText = '0.00';
            submitBtn.disabled = true;
            return;
        }

        cart.forEach((item, index) => {
            const lineTotal = item.price * item.quantity;
            total += lineTotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div style="font-weight:600;">${item.name}</div>
                    <div style="font-size:12px; color:var(--muted);">${item.generic_name || ''}</div>
                    <input type="hidden" name="items[${index}][medicine_id]" value="${item.id}">
                </td>
                <td>
                    <input type="number" name="items[${index}][quantity]" value="${item.quantity}" min="1" max="${item.stock_quantity}" class="input qty-input" data-index="${index}" style="padding: 4px 8px;">
                </td>
                <td>BDT ${lineTotal.toFixed(2)}</td>
                <td>
                    <button type="button" class="ghost-button remove-btn" data-index="${index}" style="padding: 4px; color: var(--error-text); border: none;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg></button>
                </td>
            `;
            cartBody.appendChild(tr);
        });

        cartTotalEl.innerText = total.toFixed(2);
        submitBtn.disabled = false;

        // Attach listeners to new elements
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                const idx = parseInt(this.getAttribute('data-index'));
                let val = parseInt(this.value);
                if(isNaN(val) || val < 1) val = 1;
                if(val > cart[idx].stock_quantity) val = cart[idx].stock_quantity;
                cart[idx].quantity = val;
                updateCartUI();
            });
        });

        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.getAttribute('data-index'));
                cart.splice(idx, 1);
                updateCartUI();
            });
        });
    }

    medicineInput.addEventListener('input', function() {
        clearTimeout(medicineTimer);
        const query = this.value.trim();
        
        if (query.length < 2) {
            medicineDropdown.style.display = 'none';
            return;
        }

        medicineTimer = setTimeout(() => {
            fetch(`{{ route('pharmacist.api.medicines') }}?query=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                medicineResults.innerHTML = '';
                if (data.length === 0) {
                    medicineResults.innerHTML = '<div style="padding: 12px 16px; color: var(--muted);">No matches found.</div>';
                } else {
                    data.forEach(med => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.style.cssText = 'padding: 12px 16px; cursor: pointer; border-bottom: 1px solid var(--border-light);';
                        
                        let stockLabel = med.stock_quantity > 0 ? `<span style="color:var(--success-color);">In Stock: ${med.stock_quantity}</span>` : `<span style="color:var(--error-text);">Out of Stock</span>`;
                        
                        div.innerHTML = `
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <strong>${med.name}</strong>
                                <strong>BDT ${med.price}</strong>
                            </div>
                            <div style="font-size: 12px; color: var(--muted); margin-top:4px; display:flex; justify-content:space-between;">
                                <span>${med.generic_name || 'N/A'}</span>
                                ${stockLabel}
                            </div>
                        `;
                        
                        if (med.stock_quantity > 0) {
                            div.addEventListener('click', () => {
                                // Check if already in cart
                                const existing = cart.find(i => i.id === med.id);
                                if (existing) {
                                    if(existing.quantity < med.stock_quantity) existing.quantity++;
                                } else {
                                    cart.push({...med, quantity: 1});
                                }
                                medicineInput.value = '';
                                medicineDropdown.style.display = 'none';
                                updateCartUI();
                            });
                        } else {
                            div.style.opacity = '0.5';
                            div.style.cursor = 'not-allowed';
                        }
                        
                        medicineResults.appendChild(div);
                    });
                }
                medicineDropdown.style.display = 'block';
            });
        }, 300);
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!document.getElementById('patient-search-wrapper').contains(e.target)) {
            patientDropdown.style.display = 'none';
        }
        if (!document.getElementById('medicine-search-wrapper').contains(e.target)) {
            medicineDropdown.style.display = 'none';
        }
    });

    // Initial old input population (if validation failed)
    @if(old('items'))
        // This is complex to re-hydrate fully since we need medicine names/prices.
        // For a true robust app, we'd fetch them via AJAX on load, but for now we rely on the user re-adding.
    @endif
});
</script>
@endsection
