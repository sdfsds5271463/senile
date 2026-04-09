# kube-prometheus-stack：Prometheus + Grafana + node-exporter + kube-state-metrics
# Grafana 已預先設定 Prometheus（預設）與 Loki（額外）兩個 datasource
# Alertmanager 關閉以節省 RAM

resource "helm_release" "kube_prometheus_stack" {
  name       = "kube-prometheus-stack"
  repository = "https://prometheus-community.github.io/helm-charts"
  chart      = "kube-prometheus-stack"
  version    = "82.17.1"
  namespace  = kubernetes_namespace.monitoring.metadata[0].name
  wait       = true
  timeout    = 300

  values = [<<-YAML
    # ── Prometheus ──────────────────────────────────────────────────────────
    prometheus:
      prometheusSpec:
        retention: "24h"          # production dev (無法拆開) 指標保留 24 小時
        # 開啟 Remote Write Receiver，讓 Tempo metrics-generator 可以 push 指標
        enableRemoteWriteReceiver: true
        resources:
          requests:
            memory: "512Mi"
            cpu: "200m"
          limits:
            memory: "2Gi"         # 給予足夠的爆發空間，防止大查詢 OOM
            cpu: "1000m"

    # ── Grafana ─────────────────────────────────────────────────────────────
    grafana:
      enabled: true
      adminPassword: "${var.grafana_admin_password}"
      resources:
        requests:
          memory: "256Mi"
          cpu: "100m"
        limits:
          memory: "512Mi"          # 防止渲染複雜圖表時掛掉
          cpu: "500m"

      # 持久化 Grafana 資料（dashboard、設定、annotations 全部存在 PVC）
      persistence:
        enabled: true
        size: 2Gi
        storageClassName: local-path   # k3s 預設 storage class

      # 預先掛載 Loki datasource，啟動後直接可用
      additionalDataSources:
        - name: Loki
          type: loki
          url: http://loki.monitoring.svc.cluster.local:3100
          access: proxy
          isDefault: false
          jsonData:
            maxLines: 1000

        - name: Tempo
          type: tempo
          url: http://tempo.monitoring.svc.cluster.local:3100
          access: proxy
          isDefault: false
          uid: loki
          jsonData:
            httpMethod: GET
            # Trace → Log 跳轉（點 trace 可直接跳 Loki 查對應時段 log）
            tracesToLogsV2:
              datasourceUid: loki
              spanStartTimeShift: "-1m"
              spanEndTimeShift: "1m"
              filterByTraceID: false
              filterBySpanID: false
            # Node Graph 可視化
            nodeGraph:
              enabled: true

    # ── Alertmanager（關閉，省 RAM）──────────────────────────────────────────
    alertmanager:
      enabled: false    # 目前使用 Grafana 通知，不需要開啟這

    # ── node-exporter（蒐集 VM 系統指標）────────────────────────────────────
    nodeExporter:
      enabled: true

    # ── kube-state-metrics（蒐集 k8s 物件指標）──────────────────────────────
    kubeStateMetrics:
      enabled: true
  YAML
  ]

  depends_on = [
    kubernetes_namespace.monitoring,
    helm_release.tempo,   # Tempo 必須先存在，Grafana 才能 probe datasource
  ]
}
