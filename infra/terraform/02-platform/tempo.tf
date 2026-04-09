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
  version    = "1.10.3"
  namespace  = kubernetes_namespace.monitoring.metadata[0].name
  wait       = true
  timeout    = 180

  values = [<<-YAML
    tempo:
      reportingEnabled: false

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

      # ── Span Metrics Generator ──────────────────────────────────────────────
      # 啟用後 Grafana 的 TraceQL rate()/histogram() 才能運作
      # 產生的指標 remote_write 至 Prometheus
      metrics_generator:
        ring:
          kvstore:
            store: memberlist    # single binary 用 memberlist 組成 1 節點的 ring
        processor:
          span_metrics:
            enable_target_info: true
          service_graphs:
            enable_messaging_system_latency_histogram: false
        storage:
          path: /var/tempo/generator/wal
          remote_write:
            - url: http://kube-prometheus-stack-prometheus.monitoring.svc.cluster.local:9090/api/v1/write
              send_exemplars: true

      overrides:
        defaults:
          metrics_generator:
            processors: [service-graphs, span-metrics]   # 啟用兩個 processor

      # ── 保留設定 ────────────────────────────────────────────────────────────
      compactor:
        compaction:
          block_retention: 2h      # trace 保留 2 小時（dev 環境節省磁碟）

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
