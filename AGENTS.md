# AGENTS.md

AI agent 在此專案工作時的指引與注意事項。

---

## 專案概述

Laravel 11 + Inertia.js (Vue 3) + SSR 的 Web 應用程式，部署在 k3s 單節點 Kubernetes，採用 GitOps 流程（ArgoCD）。學習用專案，目標為熟悉現代全端部署架構。

---

## 技術架構

```
前端：Vue 3 + Inertia.js + PrimeVue + Tailwind CSS
後端：Laravel 11 (PHP 8.3-fpm)
SSR：php artisan inertia:start-ssr → node bootstrap/ssr/ssr.js
DB：MySQL 8.0（StatefulSet）
快取/Session/Queue：Redis 7
容器：Docker multi-stage build（node:20-slim → php:8.3-fpm）
部署：k3s + Kustomize overlays（base / production / dev）
GitOps：ArgoCD
Secret：Bitnami Sealed Secrets（kubeseal 加密後存 git）
監控：Prometheus + Grafana + Loki + Tempo
CI/CD：GitHub Actions（self-hosted runner）
```

---

## 目錄結構重點

```
app/                    Laravel 應用程式邏輯
resources/js/           Vue 3 元件（Pages/、Components/）
resources/js/ssr.js     SSR 入口
resources/views/app.blade.php  唯一的 Blade 模板
k8s/base/               Kubernetes 基礎 manifests
k8s/overlays/production/  production 差異設定
k8s/overlays/dev/         dev 差異設定
infra/ansible/          伺服器初始化（k3s、Docker、runner）
infra/terraform/        Helm chart 部署（Prometheus、Grafana、ArgoCD）
docker/                 entrypoint.sh、migrate-with-lock.php、nginx.conf
.github/workflows/      CI/CD pipeline
```

---

## 重要規則

### Secret 管理
- **絕對不能**在 k8s yaml 裡明文寫密碼
- 所有 Secret 透過 `kubeseal` 加密後存在 `k8s/overlays/*/sealed-secret.yaml`
- `k8s/base/env.yaml` 只放非敏感的 ConfigMap，敏感值的 key 留空或加註釋

### DB Migration
- Migration 以 `docker/migrate-with-lock.php` 執行，使用 MySQL advisory lock 防止多 Pod 同時跑
- Migration user 是 `migration_dbuser`（有 DDL 權限）
- App runtime user 是 `senile_dbusr`（只有 CRUD 權限）

### Docker Build
- 生產用 `Dockerfile`（multi-stage，node-builder + php:8.3-fpm）
- 本地開發用 `Dockerfile.local`（單階段，含 Node.js）
- Stage 1 必須 COPY：`resources/`、`vite.config.js`、`tailwind.config.js`、`postcss.config.js`、`public/`、`vendor/tightenco/ziggy`

### k8s 修改原則
- 共用設定改 `k8s/base/`
- 環境差異改對應的 `k8s/overlays/`
- 不要在 base 裡寫 production 專用的值

### CI/CD 流程
```
push → PHP tests → Semgrep 安全掃描 → Docker build/push → Trivy 掃 image → 更新 kustomization.yaml → 建 PR → CodeRabbit review → Release Please
```
- Semgrep 在 Docker build 之前，有問題不推 image
- Trivy 只在 main branch 執行
- Release Please 只在 main branch 執行

---

## Commit Message 規範

遵守 Conventional Commits：

```
feat: 新增功能
fix: 修正 bug
chore: 雜項維護
refactor: 重構
docs: 文件
style: 排版
test: 測試
```

Release Please 依此自動產生 CHANGELOG.md 和 GitHub Release。

---

## 已知注意事項

- nginx container 不設 `runAsNonRoot`，設了會 CrashLoopBackOff（需寫 /var/cache/nginx）
- php-fpm 的 Dockerfile 不加 `USER`（master process 需要 root 管理 worker），以 `# nosemgrep` 標記
- k8s securityContext 的 `runAsUser: 33`（www-data）在 php container 設定，覆蓋 Dockerfile 預設
- PrimeVue v4 CSS 是 JS 動態注入，SSR 時需在 app.blade.php 預設 body 背景色防止 FOUC
- `vite.config.js` 的 input 必須明確列出 `resources/css/app.css`，否則 @vite directive 會報錯
- initdb 腳本只在 PVC 全新時執行，已有 PVC 需手動建立 DB user

---

## 本地開發

```bash
# 啟動所有服務
docker compose up -d

# 首次啟動後執行 migration + seed
php artisan migrate
php artisan db:seed

# 前端開發（在 host 跑，不在 Docker 裡）
npm run dev
```

MySQL 資料存在 named volume `mysql-data`，`docker compose down` 不會刪除。
`docker compose down -v` 才會刪除資料。
