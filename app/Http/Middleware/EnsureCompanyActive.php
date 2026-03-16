<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->with('error', 'Sua conta foi desativada. Entre em contato com o administrador.');
            }

            if ($user->isSuperAdmin()) {
                return $next($request);
            }

            if (!$user->company || !$user->company->active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->with('error', 'Sua empresa está desativada.');
            }
        }

        return $next($request);
    }
}
