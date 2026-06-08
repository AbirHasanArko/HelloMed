<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Medicine;

class MedicineController extends Controller
{
    public function index()
    {
        return view('medicines.index', [
            'medicines' => Medicine::query()->where('is_active', true)->latest()->paginate(16),
        ]);
    }

    public function show(Medicine $medicine)
    {
        abort_unless($medicine->is_active, 404);

        return view('medicines.show', compact('medicine'));
    }
}
