# 三個 namespace：
#   argocd      → ArgoCD 自身
#   production  → main 線的 Laravel 服務
#   dev         → dev 線的 Laravel 服務

resource "kubernetes_namespace" "argocd" {
  metadata {
    name = var.argocd_namespace
    labels = {
      "managed-by" = "terraform"
    }
  }
}

resource "kubernetes_namespace" "production" {
  metadata {
    name = "production"
    labels = {
      "managed-by" = "terraform"
      "env"        = "production"
    }
  }
}

resource "kubernetes_namespace" "dev" {
  metadata {
    name = "dev"
    labels = {
      "managed-by" = "terraform"
      "env"        = "dev"
    }
  }
}

resource "kubernetes_namespace" "monitoring" {
  metadata {
    name = "monitoring"
    labels = {
      "managed-by" = "terraform"
    }
  }
}
