<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    public function create(): View
    {
        return view('admin.staff.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:255'],
            'monthly_payment' => ['nullable', 'numeric', 'min:0'],
            'initial_password' => ['required', 'string', 'min:8', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $staff = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'monthly_payment' => $validated['monthly_payment'] ?? null,
            'password' => Hash::make($validated['initial_password']),
            'role' => 'staff',
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogger::log('user.role_assigned', $staff, [], [
            'role' => 'staff',
            'is_active' => $staff->is_active,
        ]);

        return redirect()->route('admin.staff.index')->with('status', 'Staff account created successfully.');
    }

    public function index(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse|View
    {
        $query = User::query()->where('role', 'staff');

        $result = User::handleSearchAndFilters($request, $query, function ($user) {
            return [
                'id' => $user->id,
                'title' => $user->name,
                'subtitle' => $user->email . ' | ' . $user->phone
            ];
        });

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return view('admin.staff.index', [
            'staff' => $result->latest()->paginate(15)->withQueryString(),
            'routePrefix' => 'admin',
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role === 'staff') {
            $user->delete();
        }

        return redirect()->route('admin.staff.index')->with('status', 'Staff deleted successfully.');
    }

    public function edit(User $user): View|RedirectResponse
    {
        if ($user->role !== 'staff') {
            return redirect()->route('admin.staff.index')->with('error', 'Invalid staff user.');
        }

        return view('admin.staff.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'staff') {
            return redirect()->route('admin.staff.index')->with('error', 'Invalid staff user.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:255'],
            'monthly_payment' => ['nullable', 'numeric', 'min:0'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'monthly_payment' => $validated['monthly_payment'] ?? null,
            'is_active' => $request->boolean('is_active', false),
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.staff.index')->with('status', 'Staff account updated successfully.');
    }
}
