<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureAccountIsActive::class);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Giriş yapmamış kullanıcı korumalı sayfaya girerse
        $middleware->redirectGuestsTo(fn () => route('login'));
        // Giriş yapmış kullanıcı giriş/kayıt sayfasına girerse
        $middleware->redirectUsersTo(fn () => route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontFlash(['card_number', 'card_cvv', 'card_holder', 'card_month', 'card_year']);
    })->create();
