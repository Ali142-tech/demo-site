<?php

use App\Http\Middleware\EnsureAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Render (and most hosts) terminate HTTPS at a proxy in front of the app.
        $middleware->trustProxies(at: '*');
        $middleware->alias(['admin' => EnsureAdmin::class]);
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo(fn () => auth()->user()?->isAdmin() ? '/admin' : '/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
