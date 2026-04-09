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
    # ── 完整 config 覆蓋（唯一能加入 metrics_generator.traces_storage 的方式）─
    # chart 的 metricsGenerator template 不支援 traces_storage，
    # local-blocks processor 必須有此欄位才能運作
    config: |
      multitenancy_enabled: false
      usage_report:
        reporting_enabled: false
      compactor:
        compaction:
          block_retention: 2h
      distributor:
        receivers:
          otlp:
            protocols:
              grpc:
                endpoint: 0.0.0.0:4317
              http:
                endpoint: 0.0.0.0:4318
      ingester: {}
      server:
        http_listen_port: 3100
      storage:
        trace:
          backend: local
          local:
            path: /var/tempo/traces
          wal:
            path: /var/tempo/wal
      querier: {}
      query_frontend: {}
      overrides:
        per_tenant_override_config: /conf/overrides.yaml
      metrics_generator:
        traces_storage:
          path: /var/tempo/wal          # local-blocks 必須指向 ingester WAL 路徑
        storage:
          path: /var/tempo/generator    # 改用 PVC 路徑，重啟後不丟失
          remote_write:
            - url: http://kube-prometheus-stack-prometheus.monitoring.svc.cluster.local:9090/api/v1/write

    # ── Per-tenant overrides（寫入 /conf/overrides.yaml）──────────────────
    tempo:
      overrides:
        "*":
          metrics_generator_processors:
            - service-graphs
            - span-metrics
            - local-blocks
          ingestion_rate_limit_bytes: 15000000
          ingestion_burst_size_bytes: 20000000

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
