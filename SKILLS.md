# SKILLS.md

此專案涉及的技術技能清單，作為學習路徑與技術範疇的參考。

---

## 後端

| 技術 | 版本 | 用途 |
|---|---|---|
| PHP | 8.3 | 主要語言 |
| Laravel | 11 | Web 框架 |
| Composer | 2.9.5 | PHP 套件管理 |
| MySQL | 8.0 | 主要資料庫 |
| Redis | 7 | Session / Cache / Queue |

### Laravel 核心概念
- Eloquent ORM
- Migration + Seeder + Factory
- Queue Worker（`queue:work`）
- Scheduler（`schedule:run`）
- Artisan commands
- Service Provider / Service Container
- Middleware

---

## 前端

| 技術 | 用途 |
|---|---|
| Vue 3 | 前端框架（Composition API）|
| TypeScript | 型別安全 |
| Inertia.js | 前後端橋接（無 API 的 SPA）|
| Vite | Build 工具 |
| Tailwind CSS | Utility-first CSS |
| PrimeVue v4 | UI 元件庫（Aura 主題）|
| Ziggy | Laravel routes 在前端使用 |

### SSR（Server-Side Rendering）
- `resources/js/ssr.js` → Inertia SSR server
- `php artisan inertia:start-ssr` → 啟動 SSR
- `ssr: { noExternal: true }` → SSR bundle 自包含，不需要 node_modules
- node binary 從 node:20-slim 複製進 php image

---

## 容器與部署

| 技術 | 用途 |
|---|---|
| Docker | 容器化 |
| Multi-stage Dockerfile | 減少 image 體積，隔離 build 環境 |
| Docker Compose | 本地開發環境 |
| Kubernetes (k3s) | 生產環境容器編排 |
| Kustomize | K8s manifest 管理（base + overlays）|
| Helm | 安裝第三方 chart（Prometheus 等）|

### Kubernetes 資源
- Deployment / StatefulSet / Service / Ingress
- ConfigMap / Secret（Sealed Secrets）
- PersistentVolumeClaim（local-path provisioner）
- PodDisruptionBudget
- initContainer（asset 複製）
- securityContext（allowPrivilegeEscalation、runAsUser）
- readinessProbe / livenessProbe / startupProbe
- terminationGracePeriodSeconds

---

## GitOps 與 CI/CD

| 技術 | 用途 |
|---|---|
| Git | 版本控制 |
| GitHub Actions | CI/CD pipeline（self-hosted runner）|
| ArgoCD | GitOps，自動偵測 git 變化並 sync 到 k8s |
| Bitnami Sealed Secrets | Secret 加密後安全存入 git |
| kubeseal | Sealed Secrets CLI |
| Docker Hub | Container registry |
| peter-evans/create-pull-request | CI 自動建 PR |
| CodeRabbit | AI Code Review |
| Semgrep | 靜態安全掃描（SAST）|
| Trivy | Container image 漏洞掃描 |
| Release Please | 自動 CHANGELOG + GitHub Release |

### CI/CD 流程
```
push → test → semgrep → docker build/push → trivy → update kustomization → PR → argocd sync
```

---

## 基礎設施

| 技術 | 用途 |
|---|---|
| Ansible | 伺服器初始化（k3s、Docker、runner 安裝）|
| Terraform | Helm chart 部署（platform 層）|
| VMware | 虛擬機（學習環境）|

---

## 監控與可觀測性

| 技術 | 用途 |
|---|---|
| Prometheus | 指標收集 |
| Grafana | 儀表板與 Alert |
| Loki | Log 收集與查詢（LogQL）|
| Tempo | Distributed Tracing |
| mysqld-exporter | MySQL 指標 → Prometheus |
| redis-exporter | Redis 指標 → Prometheus |
| nginx-prometheus-exporter | Nginx 指標 → Prometheus |

---

## 安全

- Sealed Secrets：Secret 加密存 git，cluster 解密
- `allowPrivilegeEscalation: false`：防止容器提權
- `runAsNonRoot + runAsUser: 33`：php container 以 www-data 執行
- DB 最小權限原則：migration_dbuser（DDL）/ senile_dbusr（CRUD）/ exporter（READ）
- Semgrep SAST：p/php、p/javascript、p/owasp-top-ten
- Trivy：CRITICAL 漏洞阻擋部署
- MySQL advisory lock：防止多 Pod migration race condition

---

## 學習建議路徑

```
1. Laravel 基礎（routing、Eloquent、middleware）
2. Vue 3 + Inertia.js（SPA without API）
3. Docker（Dockerfile、compose）
4. Kubernetes 基礎（Pod、Deployment、Service）
5. Kustomize（base/overlay 管理）
6. GitHub Actions CI/CD
7. GitOps with ArgoCD
8. Sealed Secrets
9. Monitoring（Prometheus + Grafana + Loki）
10. Security hardening（securityContext、SAST、image scanning）
```
