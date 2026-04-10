# Senile App

Laravel 11 + Inertia.js (Vue 3) + SSR 的全端 Web 應用程式。
學習用專案，目標為熟悉現代全端部署架構，涵蓋容器化、Kubernetes、GitOps、CI/CD、監控等完整生產流程。

---

## 技術架構

| 層級 | 技術 |
|---|---|
| 前端 | Vue 3 + Inertia.js + PrimeVue + Tailwind CSS |
| 後端 | Laravel 11 (PHP 8.3-fpm) |
| SSR | Inertia SSR（node bootstrap/ssr/ssr.js）|
| 資料庫 | MySQL 8.0 |
| 快取 / Session / Queue | Redis 7 |
| 容器 | Docker multi-stage build |
| 部署 | k3s + Kustomize（base / production / dev）|
| GitOps | ArgoCD |
| Secret | Bitnami Sealed Secrets |
| 監控 | Prometheus + Grafana + Loki + Tempo |
| CI/CD | GitHub Actions（self-hosted runner）|

---

## 本地開發

### 前置需求

- Docker + Docker Compose
- PHP 8.3 + Composer
- Node.js 20 + npm

### 啟動

```bash
# 安裝 PHP 套件
composer install

# 安裝 JS 套件
npm install

# 複製環境設定
cp .env.example .env
php artisan key:generate

# 啟動容器（MySQL、Redis、PHP、Nginx、Worker、Scheduler、SSR）
docker compose up -d

# 首次啟動：執行 migration 與 seed
php artisan migrate
php artisan db:seed
```

### 前端開發（HMR）

```bash
# 在 host 上跑，不要在 Docker 裡跑
npm run dev
```

瀏覽器開啟 http://localhost:8080

### 注意事項

- MySQL 資料存在 named volume `mysql-data`，`docker compose down` 不會刪資料
- 要重置資料庫請用 `docker compose down -v`

---

## CI/CD 流程

```
push to main/dev
    ↓
PHP Unit Tests
    ↓
Semgrep 安全掃描（SAST）
    ↓
Docker Build + Push to Docker Hub
    ↓
Trivy Image Scan（僅 main，CRITICAL 漏洞阻擋部署）
    ↓
更新 kustomization.yaml image tag
    ↓
建立 Deploy PR（CodeRabbit AI Review）
    ↓
merge PR → ArgoCD 偵測變化 → 自動 sync 到 k3s
    ↓
Release Please 更新 CHANGELOG + 建立 GitHub Release
```

---

## 部署架構

```
GitHub
  └── ArgoCD（監聽 k8s/ 目錄）
        └── k3s cluster
              ├── production namespace
              │     ├── laravel-app（php-fpm + nginx sidecar）
              │     ├── laravel-worker（queue worker）
              │     ├── laravel-scheduler
              │     ├── laravel-ssr（Inertia SSR server）
              │     ├── mysql-0（StatefulSet）
              │     └── redis
              └── dev namespace（同結構，資源規格較小）
```

---

## Secret 管理

所有 Secret 透過 `kubeseal` 加密後存入 git，由 sealed-secrets-controller 在 cluster 內解密。

```bash
# 產生 production sealed secret
kubectl create secret generic laravel-secret \
  --namespace=production \
  --from-literal=APP_KEY='...' \
  --from-literal=DB_ROOT_PASSWORD='...' \
  --from-literal=DB_PASSWORD='...' \
  --from-literal=MIGRATION_DB_PASSWORD='...' \
  --from-literal=MYSQL_EXPORTER_PASSWORD='...' \
  --dry-run=client -o yaml | \
kubeseal --controller-namespace kube-system --format yaml \
  > k8s/overlays/production/sealed-secret.yaml
```

---

## 目錄結構

```
app/                        Laravel 應用程式邏輯
resources/
  js/
    Pages/                  Inertia 頁面元件
    Components/             共用 Vue 元件
    ssr.js                  SSR 入口
  css/app.css               Tailwind CSS 入口
  views/app.blade.php       唯一的 Blade 模板
k8s/
  base/                     共用 Kubernetes manifests
  overlays/
    production/             production 環境差異
    dev/                    dev 環境差異
infra/
  ansible/                  伺服器初始化（k3s、Docker、runner）
  terraform/                Helm chart 部署（Prometheus、Grafana、ArgoCD）
docker/
  entrypoint.sh             容器啟動腳本
  migrate-with-lock.php     分散式 migration（MySQL advisory lock）
  nginx.conf                Nginx 設定
.github/workflows/
  deploy.yml                CI/CD pipeline
```

---

## Commit Message 規範

遵守 [Conventional Commits](https://www.conventionalcommits.org/)：

```
feat: 新增功能
fix: 修正 bug
chore: 雜項維護
refactor: 重構
docs: 文件
test: 測試
```

Release Please 依此自動產生 `CHANGELOG.md` 與 GitHub Release。
