output "argocd_namespace" {
  description = "ArgoCD 安裝的 namespace"
  value       = kubernetes_namespace.argocd.metadata[0].name
}

output "argocd_initial_password_command" {
  description = "取得 ArgoCD admin 初始密碼的指令（在 VM 上執行）"
  value       = "kubectl -n argocd get secret argocd-initial-admin-secret -o jsonpath='{.data.password}' | base64 -d && echo"
}

output "argocd_port_forward_command" {
  description = "開啟 ArgoCD UI 的 port-forward 指令（在 VM 上執行，瀏覽 http://localhost:8080）"
  value       = "kubectl port-forward svc/argocd-server -n argocd 8080:80"
}

output "app_namespaces" {
  description = "應用程式 namespace 清單"
  value = {
    production = kubernetes_namespace.production.metadata[0].name
    dev        = kubernetes_namespace.dev.metadata[0].name
  }
}

output "grafana_port_forward_command" {
  description = "開啟 Grafana UI 的 port-forward 指令（瀏覽 http://localhost:3000）"
  value       = "kubectl port-forward svc/kube-prometheus-stack-grafana -n monitoring 3000:80"
}

output "grafana_admin_user" {
  description = "Grafana 登入帳號"
  value       = "admin"
}
