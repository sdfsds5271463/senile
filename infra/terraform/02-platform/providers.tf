terraform {
  required_providers {
    kubernetes = {
      source  = "hashicorp/kubernetes"
      version = "~> 2.31"
    }
    helm = {
      source  = "hashicorp/helm"
      version = "~> 2.14"
    }
  }
}

# 兩個 provider 都指向同一份 kubeconfig
# Ansible phase 已將 kubeconfig 複製至 /home/allen/.kube/config
locals {
  kubeconfig_path = "/home/${var.vm_user}/.kube/config"
}

provider "kubernetes" {
  config_path = local.kubeconfig_path
}

provider "helm" {
  kubernetes {
    config_path = local.kubeconfig_path
  }
}
