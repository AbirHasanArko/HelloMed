<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::query()->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('medicine_group', 'like', "%{$search}%");
            });
        }

        if ($request->filled('group')) {
            $query->where('medicine_group', $request->group);
        }

        $medicines = $query->latest()->paginate(16)->withQueryString();

        $groups = Medicine::where('is_active', true)
            ->whereNotNull('medicine_group')
            ->where('medicine_group', '!=', '')
            ->distinct()
            ->orderBy('medicine_group')
            ->pluck('medicine_group');

        return view('medicines.index', compact('medicines', 'groups'));
    }

    public function show(Medicine $medicine)
    {
        abort_unless($medicine->is_active, 404);

        return view('medicines.show', compact('medicine'));
    }
}
