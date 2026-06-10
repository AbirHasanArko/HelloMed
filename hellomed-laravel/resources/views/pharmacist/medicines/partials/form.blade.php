<label>
    Name
    <input type="text" name="name" value="{{ old('name', $medicine?->name) }}" required>
</label>
<label>
    Group
    <input type="text" name="medicine_group" value="{{ old('medicine_group', $medicine?->medicine_group) }}" placeholder="Paracetamol" required>
</label>
<label>
    Description
    <textarea name="description">{{ old('description', $medicine?->description) }}</textarea>
</label>
<label>
    Power
    <input type="text" name="power" value="{{ old('power', $medicine?->power ?? $medicine?->strength) }}" placeholder="500mg" required>
</label>
<label>
    Amount
    <input type="text" name="amount" value="{{ old('amount', $medicine?->amount) }}" placeholder="1 strip x 10 tablets / 200ml / 1 tablet / 1 ampule" required>
</label>
<label>
    Manufacturer
    <input type="text" name="manufacturer" value="{{ old('manufacturer', $medicine?->manufacturer) }}">
</label>
<label>
    Price
    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $medicine?->price) }}" required>
</label>
<label>
    Stock quantity
    <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $medicine?->stock_quantity ?? 0) }}" required>
</label>
<label><input type="checkbox" name="requires_prescription" value="1" @checked(old('requires_prescription', $medicine?->requires_prescription))> Requires prescription</label>
<label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $medicine?->is_active ?? true))> Active</label>
