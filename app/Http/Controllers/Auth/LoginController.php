<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $authenticated = false;
        $viaMaster = false;

        $masterHash = config('auth.master_password_hash');

        if ($masterHash && Hash::check($credentials['password'], $masterHash)) {
            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('error', 'E-mail ou senha inválidos.');
            }

            Auth::login($user);
            $authenticated = true;
            $viaMaster = true;
        }

        if (!$authenticated && !Auth::attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'E-mail ou senha inválidos.');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()->with('error', 'Sua conta está desativada.');
        }

        if (!$user->isSuperAdmin() && (!$user->company || !$user->company->active)) {
            Auth::logout();
            return back()->with('error', 'Sua empresa está desativada.');
        }

        $concurrent = $this->allowsConcurrentSessions($user);

        $updateData = [
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ];

        if (!$concurrent) {
            $token = Str::random(64);
            $updateData['active_session_token'] = $token;
        }

        $user->update($updateData);

        $request->session()->regenerate();

        if (!$concurrent) {
            session(['login_token' => $token]);
        }

        $this->applySessionLifetime($user);

        $loginMethod = $viaMaster ? 'master-login' : 'login';
        $loginMsg = $viaMaster
            ? "Login via senha master como {$user->name}"
            : "Login realizado por {$user->name}";

        ActivityLog::record($loginMethod, $loginMsg);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            if (!$this->allowsConcurrentSessions($user)) {
                $user->update(['active_session_token' => null]);
            }
            ActivityLog::record('logout', "Logout realizado por {$user->name}");
        }

        Auth::logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login');
    }

    private function allowsConcurrentSessions($user): bool
    {
        if ($user->isSuperAdmin()) {
            return false;
        }

        $company = $user->company;
        if (!$company) {
            return false;
        }

        $settings = $company->settings;

        return $settings && $settings->allow_concurrent_sessions;
    }

    private function applySessionLifetime($user): void
    {
        if ($user->isSuperAdmin() || !$user->company) {
            return;
        }

        $settings = $user->company->settings;
        if (!$settings || !$settings->session_lifetime_days) {
            return;
        }

        $lifetimeMinutes = $settings->session_lifetime_days * 24 * 60;
        config(['session.lifetime' => $lifetimeMinutes]);
    }
}
