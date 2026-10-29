<?php

use App\Exceptions\PresensiException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

$isAdminArea = fn (Request $request): bool => $request->is('admin', 'admin/*');

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) use ($isAdminArea) {
        // Middleware 'auth' & 'guest' bawaan Laravel diarahkan ke halaman login/dashboard sesuai area.
        $middleware->redirectGuestsTo(fn (Request $request) => route($isAdminArea($request) ? 'admin.login' : 'siswa.login'));
        $middleware->redirectUsersTo(fn (Request $request) => route($isAdminArea($request) ? 'admin.dashboard' : 'siswa.dashboard'));

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (PresensiException $e, Request $request) {
            return $request->expectsJson()
                ? response()->json(['message' => $e->getMessage()], 422)
                : back()->with('error', $e->getMessage());
        });
    })->create();
