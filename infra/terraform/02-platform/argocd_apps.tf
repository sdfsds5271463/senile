# ArgoCD Application CRD × 2
#
# ArgoCD 的核心資源是 Application CRD（由 ArgoCD 自己定義）
# Terraform kubernetes_manifest 可以 apply 任意 CRD 物件
#
# 追蹤邏輯：
#   laravel-production → main  分支 → k8s/overlays/production → production namespace
#   laravel-dev        → dev   分支 → k8s/overlays/dev        → dev namespace
#
# syncPolicy.automated：
#   prune=true   → Git 刪除的資源，ArgoCD 也會從 k8s 刪除
#   selfHeal=true → 有人手動改了 k8s 資源，ArgoCD 自動復原

resource "kubernetes_manifest" "argocd_app_production" {
  manifest = {
    apiVersion = "argoproj.io/v1alpha1"
    kind       = "Application"

    metadata = {
      name      = "laravel-production"
      namespace = var.argocd_namespace
      labels = {
        "managed-by" = "terraform"
        "env"        = "production"
      }
    }

    spec = {
      project = "default"

      source = {
        repoURL        = var.github_repo_url
        targetRevision = "main"
        path           = "k8s/overlays/production"
      }

      destination = {
        server    = "https://kubernetes.default.svc"
        namespace = "production"
      }

      syncPolicy = {
        automated = {
          prune    = true
          selfHeal = true
        }
        syncOptions = ["CreateNamespace=false"]   # namespace 由 Terraform 管，不讓 ArgoCD 建
      }
    }
  }

  depends_on = [
    helm_release.argocd,
    kubernetes_namespace.production,
  ]
}

resource "kubernetes_manifest" "argocd_app_dev" {
  manifest = {
    apiVersion = "argoproj.io/v1alpha1"
    kind       = "Application"

    metadata = {
      name      = "laravel-dev"
      namespace = var.argocd_namespace
      labels = {
        "managed-by" = "terraform"
        "env"        = "dev"
      }
    }

    spec = {
      project = "default"

      source = {
        repoURL        = var.github_repo_url
        targetRevision = "dev"
        path           = "k8s/overlays/dev"
      }

      destination = {
        server    = "https://kubernetes.default.svc"
        namespace = "dev"
      }

      syncPolicy = {
        automated = {
          prune    = true
          selfHeal = true
        }
        syncOptions = ["CreateNamespace=false"]
      }
    }
  }

  depends_on = [
    helm_release.argocd,
    kubernetes_namespace.dev,
  ]
}
