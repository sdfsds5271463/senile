# ── Stage 1: JS/CSS 編譯 ────────────────────────────────────────────────────
# 只做 npm install + npm run build，產出 public/build
# 此 stage 的 node_modules（含 esbuild）不會進入最終 image
FROM node:20-slim AS node-builder

WORKDIR /app
COPY package*.json ./
RUN npm ci --ignore-scripts
COPY resources/ resources/
COPY vite.config.js ./
# public/build 目錄需要存在讓 Vite 輸出
COPY public/ public/
# Ziggy 是 PHP package 但有 JS 入口點，Vite build 需要讀到它
COPY vendor/tightenco/ziggy ./vendor/tightenco/ziggy
RUN npm run build

# ── Stage 2: PHP Runtime ─────────────────────────────────────────────────────
FROM php:8.3-fpm

# 1. 安裝系統套件與清理快取
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    netcat-openbsd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. 安裝 PHP 核心擴充與 Redis 擴充
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# 3. 安裝 Composer
COPY --from=composer:2.9.5 /usr/bin/composer /usr/bin/composer

# 3.5. PHP OPcache 設定 (正式環境版本)
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www

# 4. 複製 PHP 程式碼（排除 node_modules、前端原始碼）
COPY . .

# 5. 從 node-builder stage 複製編譯好的靜態資產
COPY --from=node-builder /app/public/build ./public/build
# SSR bundle（vite build --ssr 的輸出），inertia:start-ssr 需要
COPY --from=node-builder /app/bootstrap/ssr ./bootstrap/ssr

# 5.1 從 node-builder 複製 Node.js runtime（SSR 需要執行 node bootstrap/ssr/ssr.js）
# 只複製 binary，不複製 npm/node_modules，攻擊面最小
COPY --from=node-builder /usr/local/bin/node /usr/local/bin/node

# 6. 安裝 PHP 套件（no-dev，不需要 npm）
RUN composer install --optimize-autoloader --no-dev

# 7. 設定權限
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 8. 設定 Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
