<?php

namespace App\Providers;

use App\Services\TracingService;
use Illuminate\Support\ServiceProvider;

class TracingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton：整個 request 共用同一個 TracerProvider（同一個 span tree）
        $this->app->singleton(TracingService::class);
    }
}
