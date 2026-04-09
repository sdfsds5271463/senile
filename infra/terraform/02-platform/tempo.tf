# Tempo：分散式追蹤後端（Single Binary 模式）
# 接收來自 Laravel 的 OTLP trace，由 Grafana 查詢
#
# Ports:
#   3100 → HTTP API（Grafana datasource 查詢用）
#   4317 → OTLP gRPC
#   4318 → OTLP HTTP（Laravel 送 trace 用）

resource "helm_release" "tempo" {
  name       = "tempo"
  repository = "https://grafana.github.io/helm-charts"
  chart      = "tempo"
  version    = "1.14.0"   # Tempo 2.6.1，支援 compare() TraceQL 函式（2.6 新增）
  namespace  = kubernetes_namespace.monitoring.metadata[0].name
  wait       = true
  timeout    = 180

  values = [<<-YAML
    tempo:
      reportingEnabled: false

      # ── Trace 保留（chart 認識的 key 是 retention，不是 compactor.compaction.block_retention）
      retention: 2h

      # ── Trace 儲存 ──────────────────────────────────────────────────────────
      storage:
        trace:
          backend: local
          local:
            path: /var/tempo/traces
          wal:
            path: /var/tempo/wal

      # ── OTLP Receiver ──────────────────────────────────────────────────────
      distributor:
        receivers:
          otlp:
            protocols:
              http:
                endpoint: 0.0.0.0:4318
              grpc:
                endpoint: 0.0.0.0:4317

      # ── Metrics Generator（必須在 tempo.* 底下，chart template 才會渲染）──
      # chart 自動加入 service-graphs + span-metrics 到 global overrides
      metricsGenerator:
        enabled: true
        remoteWriteUrl: "http://kube-prometheus-stack-prometheus.monitoring.svc.cluster.local:9090/api/v1/write"

      # ── Per-tenant overrides（寫入 /conf/overrides.yaml）──────────────────
      overrides:
        "*":
          metrics_generator_processors:
            - service-graphs
            - span-metrics
            - local-blocks    # Drilldown Breakdown/Comparison 必須有此 processor
          # 新版預設 rate limit 為 0，必須明確設定否則所有 trace 都被擋掉
          ingestion_rate_limit_bytes: 15000000   # 15 MB/s
          ingestion_burst_size_bytes: 20000000   # 20 MB burst

    # ── 資源 ─────────────────────────────────────────────────────────────────
    resources:
      requests:
        memory: "256Mi"
        cpu: "100m"
      limits:
        memory: "512Mi"
        cpu: "500m"

    # ── 持久化 ───────────────────────────────────────────────────────────────
    persistence:
      enabled: true
      size: 2Gi
      storageClassName: local-path
  YAML
  ]

  depends_on = [kubernetes_namespace.monitoring]
}
