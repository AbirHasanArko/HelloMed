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
            'initial_password' => ['required', 'string', 'min:8', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $staff = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['initial_password']),
            'role' => 'staff',
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogger::log('user.role_assigned', $staff, [], [
            'role' => 'staff',
            'is_active' => $staff->is_active,
        ]);

        return redirect()->route('admin.dashboard')->with('status', 'Staff account created successfully.');
    }

    public function index(): View
    {
        return view('admin.staff.index', [
            'staff' => User::query()->where('role', 'staff')->latest()->paginate(15),
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role === 'staff') {
            $user->delete();
        }

        return redirect()->route('admin.staff.index')->with('status', 'Staff deleted successfully.');
    }
}
