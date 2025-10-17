<?php
// Sync schema.sql and seed.sql into the configured DB (sqlite or mysql)
// Usage: php scripts/sync_db.php

$root = dirname(__DIR__);
$schema = $root . '/francoisdcls/schema.sql';
$seed = $root . '/francoisdcls/seed.sql';

// Simple .env loader (KEY=VALUE)
$envFile = $root . '/francoisdcls/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($k, $v) = explode('=', $line, 2);
        $k = trim($k); $v = trim($v);
        if ((substr($v,0,1) === '"' && substr($v,-1) === '"') || (substr($v,0,1) === "'" && substr($v,-1) === "'")) {
            $v = substr($v,1,-1);
        }
        putenv("$k=$v");
        $_ENV[$k] = $v;
    }
}

$driver = getenv('FRANCOISDB_DRIVER') ?: null;
$sqliteFile = $root . '/francoisdcls/var/test_db.sqlite';

function execSql(PDO $pdo, string $sql)
{
    // Try exec; if fails try splitting by semicolon
    try {
        $pdo->beginTransaction();
        $pdo->exec($sql);
        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        // Fallback: split statements and execute individually
        $pdo->rollBack();
        $stmts = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
        foreach ($stmts as $s) {
            if ($s === '') continue;
            try {
                $pdo->exec($s);
            } catch (Throwable $e2) {
                // last resort: warn and continue
                fwrite(STDERR, "Statement failed: " . $e2->getMessage() . PHP_EOL);
            }
        }
        return false;
    }
}

if ($driver === 'sqlite' || ($driver === null && file_exists($sqliteFile))) {
    // Use sqlite
    try {
        @mkdir(dirname($sqliteFile), 0777, true);
        if (file_exists($sqliteFile)) @unlink($sqliteFile);
        $pdo = new PDO('sqlite:' . $sqliteFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Using SQLite DB: $sqliteFile\n";
        if (file_exists($schema)) execSql($pdo, file_get_contents($schema));
        if (file_exists($seed)) execSql($pdo, file_get_contents($seed));
        echo "SQLite sync complete.\n";
        exit(0);
    } catch (Throwable $e) {
        fwrite(STDERR, "SQLite sync failed: " . $e->getMessage() . PHP_EOL);
        exit(2);
    }
} else {
    // Use MySQL
    $host = getenv('FRANCOISDB_HOST') ?: '127.0.0.1';
    $dbname = getenv('FRANCOISDB_NAME') ?: 'francois_duclos';
    $user = getenv('FRANCOISDB_USER') ?: 'root';
    $pass = getenv('FRANCOISDB_PASS') ?: '';
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Using MySQL {$host}/{$dbname}\n";
        if (file_exists($schema)) execSql($pdo, file_get_contents($schema));
        if (file_exists($seed)) execSql($pdo, file_get_contents($seed));
        echo "MySQL sync complete.\n";
        exit(0);
    } catch (Throwable $e) {
        fwrite(STDERR, "MySQL sync failed: " . $e->getMessage() . PHP_EOL);
        exit(3);
    }
}
