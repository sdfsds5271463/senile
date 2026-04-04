variable "vm_user" {
  description = "VM 的 Linux 使用者名稱"
  type        = string
  default     = "allen"
}

variable "github_repo_url" {
  description = "ArgoCD 追蹤的 GitHub 倉庫 URL"
  type        = string
  default     = "https://github.com/sdfsds5271463/senile"
}

variable "argocd_namespace" {
  description = "ArgoCD 安裝的 namespace"
  type        = string
  default     = "argocd"
}

variable "argocd_chart_version" {
  description = "ArgoCD Helm chart 版本"
  type        = string
  default     = "9.4.17"
}

variable "grafana_admin_password" {
  description = "Grafana admin 登入密碼"
  type        = string
  sensitive   = true
  default     = "admin"   # 請在 terraform.tfvars 覆寫
}
