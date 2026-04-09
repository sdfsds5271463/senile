<?php

namespace App\Http\Middleware;

use App\Services\TracingService;
use Closure;
use Illuminate\Http\Request;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\StatusCode;
use Symfony\Component\HttpFoundation\Response;

class TracingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $tracing = app(TracingService::class);
        if (!$tracing->isEnabled()) {
            return $next($request);
        }

        // 從 incoming headers 萃取 W3C TraceContext（支援上游傳入的 traceparent）
        $carrier = [];
        foreach ($request->headers->all() as $key => $values) {
            $carrier[$key] = implode(',', $values);
        }
        $context = TraceContextPropagator::getInstance()->extract($carrier);

        $route  = $request->route()?->getName()
                  ?? $request->route()?->uri()
                  ?? $request->path();
        $method = $request->method();

        $span = $tracing->getTracer()
            ->spanBuilder("$method $route")
            ->setParent($context)
            ->setSpanKind(SpanKind::KIND_SERVER)
            ->startSpan();

        // activate() 將此 span 設為「目前 context」
        // Controller 建立的子 span 會自動以此為 parent
        $scope = $span->activate();

        try {
            $response = $next($request);

            $span->setAttributes([
                'http.method'      => $method,
                'http.route'       => $route,
                'http.url'         => $request->url(),
                'http.status_code' => $response->getStatusCode(),
            ]);

            if ($response->getStatusCode() >= 500) {
                $span->setStatus(StatusCode::STATUS_ERROR);
            }

            return $response;
        } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
            throw $e;
        } finally {
            $span->end();
            $scope->detach();
        }
    }
}
