<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Prometheus\Facades\Prometheus;
use Illuminate\Support\Facades\Redis;

class PrometheusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // 保持空白，讓 Spatie 套件原生的 Binding 運作即可
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //Prometheus 測試指標
        //TEST:  http://127.0.0.1:8080/prometheus
        Prometheus::addGauge('laravel_test_metric')
            ->value(fn() => 777);

        // 監控 DB 特定表的資料筆數
        Prometheus::addGauge('laravel_table_users_count')
            ->value(fn() => \DB::table('users')->count());

        // 監控 PHP 執行環境的記憶體
        Prometheus::addGauge('laravel_php_memory_usage_bytes')
            ->value(fn() => memory_get_usage());

        // 進階：PHP 記憶體峰值 (Peak)
        Prometheus::addGauge('laravel_php_memory_peak_bytes')
            ->value(fn() => memory_get_peak_usage());

        // 監控 Redis 鍵值數量（如果你的 Cache 很滿，這會飆高）
        Prometheus::addGauge('laravel_redis_keys_total')
            ->value(fn() => Redis::dbsize());
    }
}
