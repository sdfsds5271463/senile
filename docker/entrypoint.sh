#!/bin/sh

# 1. 只有當傳入的指令是 php-fpm 時，才執行資料庫初始化
# 這樣 worker 或 ssr 啟動時就不會重複跑 migrate
if [ "$1" = "php-fpm" ]; then

    # 這裡使用 nc (netcat) 偵測 mysql 容器的 3306 埠
    # $DB_HOST 和 $DB_PORT 通常來自你的 .env
    # 如果沒定義，預設通常是 mysql 和 3306
    echo "Checking database connection..."
    while ! nc -z ${DB_HOST:-mysql} ${DB_PORT:-3306}; do
      echo "Database (${DB_HOST:-mysql}) is not available yet - waiting..."
      sleep 5
    done
    echo "Database is up! Initializing..."

    echo "Running migrations..."
    php artisan migrate --force

    echo "Running AllenUserSeeder..."
    php artisan db:seed --class=AllenUserSeeder --force
fi

# 2. 執行原本要跑的指令 (可能是 php-fpm, 也可能是 artisan queue:work 等)
echo "Starting command: $@"
exec "$@"