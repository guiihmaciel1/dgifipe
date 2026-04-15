<?php

use App\Http\Middleware\ApiTokenAuth;
use App\Http\Middleware\CompanySessionLifetime;
use App\Http\Middleware\EnforceSingleLogin;
use App\Http\Middleware\EnsureCompanyActive;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

        $middleware->alias([
            'api-token' => ApiTokenAuth::class,
            'single-login' => EnforceSingleLogin::class,
            'company-active' => EnsureCompanyActive::class,
            'company-session' => CompanySessionLifetime::class,
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()->route('login')
                ->with('error', 'Sessão expirada. Faça login novamente.');
        });
    })->create();
