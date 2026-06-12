<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class AdminMedicineController extends Controller
{
    public function index(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\View
    {
        $query = Medicine::query();

        $result = Medicine::handleSearchAndFilters($request, $query, function ($medicine) {
            return [
                'id' => $medicine->id,
                'title' => $medicine->name,
                'subtitle' => $medicine->medicine_group . ' | ' . $medicine->manufacturer
            ];
        });

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return view('pharmacist.medicines.index', [
            'medicines' => $result->latest()->paginate(15)->withQueryString(),
            'routePrefix' => 'admin',
        ]);
    }

    public function create()
    {
        return view('pharmacist.medicines.create', [
            'routePrefix' => 'admin',
        ]);
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
            'buying_price' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'requires_prescription' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('medicines', 'public');
        }

        $medicine = Medicine::query()->create([
            ...$validated,
            'strength' => $validated['power'],
            'requires_prescription' => $request->boolean('requires_prescription'),
            'is_active' => $request->boolean('is_active', true),
            'image_path' => $imagePath,
        ]);

        AuditLogger::log('medicine.created', $medicine, [], [
            'name' => $medicine->name,
            'power' => $medicine->power,
            'amount' => $medicine->amount,
            'buying_price' => $medicine->buying_price,
            'price' => $medicine->price,
            'stock_quantity' => $medicine->stock_quantity,
        ]);

        return redirect()->route('admin.medicines.index')->with('status', 'Medicine created.');
    }

    public function edit(Medicine $medicine)
    {
        return view('pharmacist.medicines.edit', [
            'medicine' => $medicine,
            'routePrefix' => 'admin',
        ]);
    }

    public function update(Request $request, Medicine $medicine)
    {
        $old = $medicine->only(['name', 'power', 'amount', 'buying_price', 'price', 'stock_quantity', 'is_active', 'requires_prescription']);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'medicine_group' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'power' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'buying_price' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'requires_prescription' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = $medicine->image_path;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('medicines', 'public');
        }

        $medicine->update([
            ...$validated,
            'strength' => $validated['power'],
            'requires_prescription' => $request->boolean('requires_prescription'),
            'is_active' => $request->boolean('is_active'),
            'image_path' => $imagePath,
        ]);

        AuditLogger::log('medicine.updated', $medicine, $old, $medicine->only(['name', 'power', 'amount', 'buying_price', 'price', 'stock_quantity', 'is_active', 'requires_prescription']));

        return redirect()->route('admin.medicines.index')->with('status', 'Medicine updated.');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        AuditLogger::log('medicine.deleted', $medicine, $medicine->toArray(), []);
        return redirect()->route('admin.medicines.index')->with('status', 'Medicine deleted.');
    }
}
