<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompanySessionLifetime
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->isSuperAdmin() && $user->company) {
                $settings = $user->company->settings;

                if ($settings && $settings->session_lifetime_days) {
                    $lifetimeMinutes = $settings->session_lifetime_days * 24 * 60;
                    config(['session.lifetime' => $lifetimeMinutes]);
                }
            }
        }

        return $next($request);
    }
}
