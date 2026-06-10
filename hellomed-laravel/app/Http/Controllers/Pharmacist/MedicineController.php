<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index()
    {
        return view('pharmacist.medicines.index', [
            'medicines' => Medicine::query()->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('pharmacist.medicines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'medicine_group' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'power' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'requires_prescription' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $medicine = Medicine::query()->create([
            ...$validated,
            'strength' => $validated['power'],
            'requires_prescription' => $request->boolean('requires_prescription'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogger::log('medicine.created', $medicine, [], [
            'name' => $medicine->name,
            'power' => $medicine->power,
            'amount' => $medicine->amount,
            'price' => $medicine->price,
            'stock_quantity' => $medicine->stock_quantity,
        ]);

        return redirect()->route('pharmacist.medicines.index')->with('status', 'Medicine created.');
    }

    public function edit(Medicine $medicine)
    {
        return view('pharmacist.medicines.edit', compact('medicine'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $old = $medicine->only(['name', 'power', 'amount', 'price', 'stock_quantity', 'is_active', 'requires_prescription']);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'medicine_group' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'power' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'requires_prescription' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $medicine->update([
            ...$validated,
            'strength' => $validated['power'],
            'requires_prescription' => $request->boolean('requires_prescription'),
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLogger::log('medicine.updated', $medicine, $old, $medicine->only(['name', 'power', 'amount', 'price', 'stock_quantity', 'is_active', 'requires_prescription']));

        return redirect()->route('pharmacist.medicines.index')->with('status', 'Medicine updated.');
    }
}
