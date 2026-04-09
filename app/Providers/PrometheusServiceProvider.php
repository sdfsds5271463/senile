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
        // 覆蓋 Spatie 的 CollectorRegistry binding，使用修正後的 Adapter
        // 修正 LaravelCacheAdapter::collect() 沒有 assign fetch() 回傳值的 bug
        $this->app->scoped(\Prometheus\CollectorRegistry::class, function () {
            $adapter = new \App\Extensions\FixedPrometheusAdapter(
                \Illuminate\Support\Facades\Cache::resolve('redis')
            );
            return new \Prometheus\CollectorRegistry($adapter, false);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //Prometheus 測試指標
        //TEST:  http://127.0.0.1:8080/prometheus
        Prometheus::addGauge('test_metric')
            ->value(fn() => 777);

        // 監控 DB 特定表的資料筆數
        Prometheus::addGauge('table_users_count')
            ->value(fn() => \DB::table('users')->count());

        // 監控 PHP 執行環境的記憶體
        Prometheus::addGauge('php_memory_usage_bytes')
            ->value(fn() => memory_get_usage());

        // 進階：PHP 記憶體峰值 (Peak)
        Prometheus::addGauge('php_memory_peak_bytes')
            ->value(fn() => memory_get_peak_usage());

        // 監控 Redis 鍵值數量（如果你的 Cache 很滿，這會飆高）
        Prometheus::addGauge('redis_keys_total')
            ->value(fn() => Redis::dbsize());

        // HTTP 請求指標由 PrometheusMetricsMiddleware 直接寫入底層 CollectorRegistry（Redis）
        // 不需要在這裡宣告，getMetricFamilySamples() 會自動讀取 Redis 所有指標
    }
}
