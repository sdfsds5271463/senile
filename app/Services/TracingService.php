<?php

namespace App\Services;

use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

class TracingService
{
    private ?TracerProvider $tracerProvider = null;
    private ?TracerInterface $tracer = null;

    public function __construct()
    {
        $endpoint = config('tracing.endpoint');
        if (!$endpoint) {
            return; // TEMPO_ENDPOINT 未設定，tracing 停用
        }

        try {
            $transport = (new OtlpHttpTransportFactory())->create(
                rtrim($endpoint, '/') . '/v1/traces',
                'application/json'  // JSON over HTTP，不需要 protobuf
            );

            $this->tracerProvider = new TracerProvider(
                new SimpleSpanProcessor(new SpanExporter($transport)),
                null,
                ResourceInfo::create(Attributes::create([
                    'service.name'           => config('app.name', 'laravel'),
                    'deployment.environment' => config('app.env', 'production'),
                ]))
            );
        } catch (\Throwable $e) {
            report($e); // 记录错误但不中断应用
            // tracing 保持停用状态
        }

        $this->tracer = $this->tracerProvider->getTracer('laravel');

        // PHP 是 share-nothing 模型，shutdown() 確保 span buffer 在 request 結束前送出
        register_shutdown_function(fn() => $this->tracerProvider?->shutdown());
    }

    public function isEnabled(): bool
    {
        return $this->tracer !== null;
    }

    public function getTracer(): ?TracerInterface
    {
        return $this->tracer;
    }
}
