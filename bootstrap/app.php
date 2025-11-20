<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/deploy.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'scope.unit' => \App\Http\Middleware\ScopeUnit::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'compress.response' => \App\Http\Middleware\CompressResponse::class,
        ]);
        
        // Aplicar headers de segurança apenas em produção
        // Usar env() diretamente pois app() ainda não está disponível aqui
        if (env('APP_ENV') === 'production') {
            $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
            $middleware->append(\App\Http\Middleware\CompressResponse::class);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Reportar exceções para Sentry em produção
        if (env('APP_ENV') === 'production') {
            $exceptions->report(function (\Throwable $e) {
                if (app()->bound('sentry')) {
                    app('sentry')->captureException($e);
                }
            });
        }
    })->create();
