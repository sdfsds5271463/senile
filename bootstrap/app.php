<?php

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
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Tracing：web + api 都要追蹤，prepend 確保第一個執行（能計到完整耗時）
        $middleware->web(prepend: [\App\Http\Middleware\TracingMiddleware::class]);
        $middleware->api(prepend: [\App\Http\Middleware\TracingMiddleware::class]);

        $middleware->alias([
            'test'               => \App\Http\Middleware\TestMiddleware::class,
            'prometheus.metrics' => \App\Http\Middleware\PrometheusMetricsMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
