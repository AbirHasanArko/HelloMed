<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AvailableTest;
use Illuminate\Validation\Rule;

class AvailableTestController extends Controller
{
    public function index(Request $request)
    {
        $query = AvailableTest::query();

        $result = AvailableTest::handleSearchAndFilters($request, $query, function ($test) {
            return [
                'id' => $test->id,
                'title' => $test->name,
                'subtitle' => $test->category . ' | $' . $test->price
            ];
        });

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return view('admin.available_tests.index', [
            'tests' => $result->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.available_tests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:available_tests,name',
            'description' => 'nullable|string',
            'lab_room_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'fee_bdt' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        AvailableTest::create($validated + ['is_active' => $request->has('is_active')]);

        return redirect()->route('admin.available-tests.index')
            ->with('status', 'Test created successfully.');
    }

    public function edit(AvailableTest $availableTest)
    {
        return view('admin.available_tests.edit', compact('availableTest'));
    }

    public function update(Request $request, AvailableTest $availableTest)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('available_tests')->ignore($availableTest->id)],
            'description' => 'nullable|string',
            'lab_room_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'fee_bdt' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $availableTest->update($validated + ['is_active' => $request->has('is_active')]);

        return redirect()->route('admin.available-tests.index')
            ->with('status', 'Test updated successfully.');
    }

    public function destroy(AvailableTest $availableTest)
    {
        $availableTest->delete();

        return redirect()->route('admin.available-tests.index')
            ->with('status', 'Test deleted successfully.');
    }
}
