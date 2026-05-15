<?php

use App\Http\Middleware\EnsureTtdExists;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->registered(function ($app) {
        if (env('VERCEL')) {
            $app->useStoragePath('/tmp');
            
            // Redirect package manifest agar tidak menulis ke bootstrap/cache
            $app->instance('manifest.path', '/tmp/packages.php');
        }
    })
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->web(append: [
            EnsureTtdExists::class,
            \App\Http\Middleware\LogActivity::class,
            \App\Http\Middleware\SetActivePeriode::class,
            \App\Http\Middleware\CheckPeriodeLock::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 419: Sesi habis / CSRF token mismatch → redirect ke login
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, Request $request) {
            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        });
    })->create();
