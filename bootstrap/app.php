<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '/',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: ['*']);

        $middleware->append(ForceJsonResponse::class);

        $middleware->api(append: [
            'throttle:10000,1'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
