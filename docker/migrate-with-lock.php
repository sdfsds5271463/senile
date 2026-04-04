<?php
/**
 * Migration runner with MySQL advisory lock.
 *
 * 解決多 pod 同時啟動時的 race condition：
 * - 第一個搶到 lock 的 pod 跑 migrate，若實際有 migration 被執行才跑 seeder
 * - 其餘 pod 等待，拿到 lock 後 migrate --force 發現 0 pending，直接結束
 *
 * 使用 MIGRATION_DB_USERNAME / MIGRATION_DB_PASSWORD（具有 DDL 權限）連線。
 */

chdir('/var/www');

$host = getenv('DB_HOST') ?: 'mysql';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_DATABASE') ?: 'senile_db';
$user = getenv('MIGRATION_DB_USERNAME');
$pass = getenv('MIGRATION_DB_PASSWORD');

if (!$user || !$pass) {
    echo "ERROR: MIGRATION_DB_USERNAME / MIGRATION_DB_PASSWORD not set.\n";
    exit(1);
}

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    echo "ERROR: Cannot connect as migration user: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Acquiring migration lock (timeout 120s)...\n";
$result = $pdo->query("SELECT GET_LOCK('laravel_migration_lock', 120)")->fetchColumn();

if ($result != 1) {
    echo "ERROR: Migration lock timed out after 120s. Schema may not be ready.\n";
    exit(1);
}

// 取得 lock 後，直接查 migrations 表現有幾筆，migrate 後比對是否增加
// 比解析 migrate:status 文字輸出更可靠
try {
    $countBefore = (int) $pdo->query("SELECT COUNT(*) FROM migrations")->fetchColumn();
} catch (PDOException $e) {
    // migrations 表還不存在 = 全新資料庫
    $countBefore = 0;
}

echo "Running migrations...\n";
passthru('php /var/www/artisan migrate --force', $exitCode);

if ($exitCode !== 0) {
    $pdo->query("SELECT RELEASE_LOCK('laravel_migration_lock')");
    exit($exitCode);
}

try {
    $countAfter = (int) $pdo->query("SELECT COUNT(*) FROM migrations")->fetchColumn();
} catch (PDOException $e) {
    $countAfter = 0;
}

$newMigrations = $countAfter - $countBefore;

if ($newMigrations > 0) {
    echo "Ran {$newMigrations} migration(s). Running AllenUserSeeder...\n";
    passthru('php /var/www/artisan db:seed --class=AllenUserSeeder --force', $exitCode);

    if ($exitCode !== 0) {
        $pdo->query("SELECT RELEASE_LOCK('laravel_migration_lock')");
        exit($exitCode);
    }
} else {
    echo "No new migrations. Skipping seeder.\n";
}

$pdo->query("SELECT RELEASE_LOCK('laravel_migration_lock')");
echo "Migration lock released.\n";

exit(0);
