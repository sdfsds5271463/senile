# ArgoCD 安裝（Helm）
# 官方 chart：https://artifacthub.io/packages/helm/argo/argo-cd
#
# values 重點：
#   - server.insecure=true  → 因為我們用 traefik ingress 做 TLS 終止，argocd server 本身不需要 HTTPS
#   - 當前階段不開 ingress，用 port-forward 存取 UI

resource "helm_release" "argocd" {
  name       = "argocd"
  repository = "https://argoproj.github.io/argo-helm"
  chart      = "argo-cd"
  version    = var.argocd_chart_version
  namespace  = kubernetes_namespace.argocd.metadata[0].name

  # 等待所有 pod 就緒才算 apply 完成
  wait    = true
  timeout = 300   # 秒，初次拉 image 可能需要一點時間

  set {
    name  = "server.insecure"
    value = "true"   # 讓 argocd-server 接受 HTTP，traefik 負責 HTTPS
  }

  # 讓 ArgoCD 有權限在其他 namespace 建立資源（production / dev）
  set {
    name  = "configs.params.application.namespaces"
    value = "production\\,dev"
  }

  depends_on = [kubernetes_namespace.argocd]
}
