<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminDepartmentController extends Controller
{
    public function index(): View
    {
        return view('admin.departments.index', [
            'departments' => Department::query()->withCount('doctors')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['required', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'service_scope' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'featured_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('department-images', 'public');
        }

        Department::query()->create([
            ...collect($validated)->except('image')->all(),
            'image_path' => $imagePath,
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
            'featured_order' => $request->integer('featured_order', 0),
        ]);

        return redirect()->route('admin.departments.index')->with('status', 'Department created successfully.');
    }

    public function edit(Department $department): View
    {
        return view('admin.departments.edit', [
            'department' => $department,
        ]);
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,'.$department->id],
            'description' => ['required', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'service_scope' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'featured_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $imagePath = $department->image_path;
        if ($request->hasFile('image')) {
            if (filled($department->image_path)) {
                Storage::disk('public')->delete($department->image_path);
            }
            $imagePath = $request->file('image')->store('department-images', 'public');
        }

        $department->update([
            ...collect($validated)->except('image')->all(),
            'image_path' => $imagePath,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured', false),
            'featured_order' => $request->integer('featured_order', 0),
        ]);

        return redirect()->route('admin.departments.index')->with('status', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if (filled($department->image_path)) {
            Storage::disk('public')->delete($department->image_path);
        }
        $department->delete();

        return redirect()->route('admin.departments.index')->with('status', 'Department deleted successfully.');
    }
}
