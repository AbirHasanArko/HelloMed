<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('departments.index', [
            'departments' => Department::query()->withCount('doctors')->latest()->get(),
        ]);
    }

    public function show(Department $department)
    {
        $department->load(['doctors' => fn ($query) => $query->where('is_active', true)->latest()]);

        return view('departments.show', compact('department'));
    }
}
