<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Admin; // Admin middleware
use App\Http\Middleware\GuestOrVerified;
use App\Http\Middleware\Cors; // Custom CORS middleware

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register the middleware aliases
        $middleware->alias([
            'guestOrVerified' => GuestOrVerified::class,
            'admin' => Admin::class,
            'cors' => Cors::class, // Register the CORS middleware
        ]);

        // Apply CORS middleware globally
        $middleware->append(Cors::class);
    })
    ->withExceptions(function ($exceptions) {
        // Handle exceptions if necessary
    })->create();
