# Loki：日誌聚合
# 使用 SingleBinary 模式（單 pod，適合小型 VM）
# 保留規則：
#   - 預設（production）：4 小時
#   - dev namespace：2 小時（per-stream retention）

resource "helm_release" "loki" {
  name       = "loki"
  repository = "https://grafana.github.io/helm-charts"
  chart      = "loki"
  version    = "6.55.0"
  namespace  = kubernetes_namespace.monitoring.metadata[0].name
  wait       = true
  timeout    = 180

  values = [<<-YAML
    deploymentMode: SingleBinary

    loki:
      auth_enabled: false

      commonConfig:
        replication_factor: 1

      storage:
        type: filesystem

      schemaConfig:
        configs:
          - from: "2024-01-01"
            store: tsdb
            object_store: filesystem
            schema: v13
            index:
              prefix: loki_index_
              period: 24h

      # ── 保留設定 ──────────────────────────────────────────────────────────
      limits_config:
        retention_period: 30h          # 預設保留 30 小時（production）
        retention_stream:
          - selector: '{namespace="dev"}'
            priority: 1
            period: 24h                # dev namespace 保留 24 小時

      compactor:
        retention_enabled: true
        working_directory: /var/loki/compactor
        compaction_interval: 5m       # 每 5 分鐘清理一次過期日誌
        delete_request_store: filesystem

    # SingleBinary 模式資源設定
    singleBinary:
      replicas: 1
      resources:
        requests:
          memory: "256Mi"
          cpu: "100m"
        limits:
          memory: "1Gi"            # 搜尋 logs 需要較多 RAM
          cpu: "500m"
      persistence:
        enabled: true
        size: 2Gi

    # 關閉分散式模式元件（SingleBinary 不需要）
    read:
      replicas: 0
    write:
      replicas: 0
    backend:
      replicas: 0

    # 關閉 memcached cache（預設 9830Mi，小型 VM 無法負擔）
    # 短時間保留（30h）不需要 cache 加速
    chunksCache:
      enabled: false
    resultsCache:
      enabled: false
  YAML
  ]

  depends_on = [kubernetes_namespace.monitoring]
}

# ── Promtail：從所有 Pod 蒐集日誌送往 Loki ──────────────────────────────────
# DaemonSet 模式：每個 node 跑一個 promtail，自動蒐集所有 namespace 的 pod log

resource "helm_release" "promtail" {
  name       = "promtail"
  repository = "https://grafana.github.io/helm-charts"
  chart      = "promtail"
  version    = "6.17.1"
  namespace  = kubernetes_namespace.monitoring.metadata[0].name
  wait       = true
  timeout    = 120

  values = [<<-YAML
    config:
      clients:
        - url: http://loki.monitoring.svc.cluster.local:3100/loki/api/v1/push

    resources:
      requests:
        memory: "64Mi"
        cpu: "50m"
      limits:
        memory: "128Mi"
        cpu: "100m"
  YAML
  ]

  depends_on = [helm_release.loki]
}
