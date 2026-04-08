<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Symfony\Component\HttpFoundation\Response;

class PrometheusMetricsMiddleware
{
    // Histogram 分桶（秒）
    private const BUCKETS = [0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1.0, 2.5, 5.0];

    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $start;
        $route    = $request->route()?->getName()
                    ?? $request->route()?->uri()
                    ?? 'unknown';
        $method   = $request->method();
        $status   = (string) $response->getStatusCode();

        try {
            $registry = app(CollectorRegistry::class);

            // Counter：每個請求 +1，labels = [method, route, status]
            // → QPS:       rate(laravel_http_requests_total[1m])
            // → Error Rate: rate(laravel_http_requests_total{status=~"5.."}[1m])
            //               / rate(laravel_http_requests_total[1m])
            $registry
                ->getOrRegisterCounter('laravel', 'http_requests_total', 'Total HTTP requests', ['method', 'route', 'status'])
                ->inc([$method, $route, $status]);

            // Histogram：記錄請求耗時，labels = [method, route]
            // → P95 Latency: histogram_quantile(0.95, rate(laravel_http_request_duration_seconds_bucket[5m]))
            $registry
                ->getOrRegisterHistogram('laravel', 'http_request_duration_seconds', 'HTTP request duration in seconds', ['method', 'route'], self::BUCKETS)
                ->observe($duration, [$method, $route]);

        } catch (\Throwable) {
            // Prometheus 寫入失敗不中斷正常請求
        }

        /*
        Grafana PromQL：

            # QPS
            rate(laravel_http_requests_total[1m])

            # P95 Latency（真正的 Histogram 百分位）
            histogram_quantile(0.95, rate(laravel_http_request_duration_seconds_bucket[5m]))

            # Error Rate (5xx)
            rate(laravel_http_requests_total{status=~"5.."}[1m])
            / rate(laravel_http_requests_total[1m])
        */

        return $response;
    }
}
