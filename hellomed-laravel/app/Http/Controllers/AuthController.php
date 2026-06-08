<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = $this->throttleKey($request);
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $retryAfter = RateLimiter::availableIn($throttleKey);

            AuditLog::query()->create([
                'actor_user_id' => null,
                'action' => 'auth.login_locked',
                'entity_type' => 'User',
                'entity_id' => null,
                'meta' => [
                    'email' => $credentials['email'],
                    'retry_after_seconds' => $retryAfter,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$retryAfter} seconds.",
            ])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            AuditLog::query()->create([
                'actor_user_id' => null,
                'action' => 'auth.login_failed',
                'entity_type' => 'User',
                'entity_id' => null,
                'meta' => [
                    'email' => $credentials['email'],
                    'reason' => 'invalid_credentials',
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        AuditLogger::log('auth.login_success', $request->user(), [], [
            'role' => $request->user()->role,
        ]);

        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($request->user()->isStaff()) {
            return redirect()->route('staff.dashboard');
        }

        if ($request->user()->isPharmacist()) {
            return redirect()->route('pharmacist.dashboard');
        }

        if ($request->user()->isDoctor()) {
            return redirect()->route('doctor.dashboard');
        }

        return redirect()->route('home')->with('status', 'Logged in successfully.');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'patient',
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        AuditLogger::log('auth.registered', $user, [], [
            'role' => $user->role,
        ]);

        return redirect()->route('home')->with('status', 'Account created successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        if ($request->user()) {
            AuditLogger::log('auth.logout', $request->user(), [], [
                'role' => $request->user()->role,
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Logged out.');
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower((string) $request->input('email')).'|'.$request->ip();
    }
}
