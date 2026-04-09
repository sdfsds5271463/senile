<?php

return [
    /*
     * Tempo OTLP HTTP endpoint
     * 設定後自動啟用 tracing，留空則停用
     *
     * 本機開發：留空（不送）
     * k8s 環境：http://tempo.monitoring.svc.cluster.local:4318
     */
    'endpoint' => env('TEMPO_ENDPOINT', ''),
];
