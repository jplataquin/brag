<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'payments/webhook',
        ]);

        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );

        $middleware->web(append: [
            \App\Http\Middleware\CheckSuspension::class,
            \App\Http\Middleware\CheckParentalConsent::class,
            \App\Http\Middleware\MaintenanceMode::class,
        ]);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'terms.agreed' => \App\Http\Middleware\EnsureTermsAgreed::class,
            'privacy.agreed' => \App\Http\Middleware\EnsurePrivacyAgreed::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
