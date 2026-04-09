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
      # 啟用後 chart 自動加入 service-graphs + span-metrics processor
      # 以及 metrics_generator.storage.remote_write 設定
      metricsGenerator:
        enabled: true
        remoteWriteUrl: "http://kube-prometheus-stack-prometheus.monitoring.svc.cluster.local:9090/api/v1/write"

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
