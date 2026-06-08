<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $doctors = Doctor::query()
            ->with('department')
            ->where('is_active', true)
            ->when($request->filled('department'), function ($query) use ($request): void {
                $query->whereHas('department', fn ($departmentQuery) => $departmentQuery->where('slug', $request->input('department')));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $departments = \App\Models\Department::query()->where('is_active', true)->orderBy('name')->get();

        return view('doctors.index', compact('doctors', 'departments'));
    }

    public function show(Doctor $doctor)
    {
        $doctor->load([
            'department',
            'appointments',
            'reviews.user',
        ]);

        $averageRating = round((float) $doctor->reviews()->avg('rating'), 1);

        return view('doctors.show', compact('doctor', 'averageRating'));
    }
}
