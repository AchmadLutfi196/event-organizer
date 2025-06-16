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
        // Tambahkan alias middleware di sini
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
            'domain' => \App\Http\Middleware\DomainMiddleware::class,
        ]);
        
        // Add global middleware for domain handling
        $middleware->web(append: [
            \App\Http\Middleware\DomainMiddleware::class,
        ]);
        
        // Exclude Midtrans callback from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'frontend/peminjaman/payment/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();