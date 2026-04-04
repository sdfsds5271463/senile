<?php
/**
 * Migration runner with MySQL advisory lock.
 *
 * 解決多 pod 同時啟動時的 race condition：
 * - 第一個搶到 lock 的 pod 跑 migrate + seed
 * - 其餘 pod 等待最多 120s，拿到 lock 後 migrate --force 發現沒有 pending，直接結束
 *
 * 使用 MIGRATION_DB_USERNAME / MIGRATION_DB_PASSWORD（具有 DDL 權限）連線。
 */

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
    echo "Migration lock timed out. Skipping migrations.\n";
    exit(0);
}

echo "Lock acquired. Running migrations...\n";
passthru('php artisan migrate --force', $exitCode);

if ($exitCode !== 0) {
    $pdo->query("SELECT RELEASE_LOCK('laravel_migration_lock')");
    exit($exitCode);
}

echo "Running AllenUserSeeder...\n";
passthru('php artisan db:seed --class=AllenUserSeeder --force', $exitCode);

$pdo->query("SELECT RELEASE_LOCK('laravel_migration_lock')");
echo "Migration lock released.\n";

exit($exitCode);
