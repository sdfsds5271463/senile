# 使用 PHP 8.3 FPM 作為基底
FROM php:8.3-fpm

# 1. 安裝系統套件、Node.js 與清理快取 (合併指令縮減體積)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    netcat-openbsd \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. 安裝 PHP 核心擴充 與 Redis 擴充 (解決 Class "Redis" not found 的關鍵)
# 注意：pecl install redis 後必須跑 docker-php-ext-enable 才會生效
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# 3. 安裝 Composer
COPY --from=composer:2.9.5 /usr/bin/composer /usr/bin/composer

# 3.5. docker 使用 php 快取 (正式環境與測試環境不同)
COPY docker/php/opcache.dev.ini /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www

# 4. 複製程式碼
COPY . .

# 5. 安裝 PHP 與 JS 套件
RUN composer install --optimize-autoloader --no-dev \
    && npm install \
    && npm run build

# 6. 設定權限 (確保 storage 具備寫入權限)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 7. 設定 Entrypoint 腳本
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

# 預設指令 (啟動 php-fpm)
CMD ["php-fpm"]