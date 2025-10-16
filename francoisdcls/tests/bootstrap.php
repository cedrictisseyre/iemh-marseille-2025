<?php
// Bootstrap for PHPUnit tests: create a temporary SQLite database for isolation.
$dbFile = __DIR__ . '/../var/test_db.sqlite';
@mkdir(dirname($dbFile), 0777, true);
if (file_exists($dbFile)) {
    unlink($dbFile);
}
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Import schema and seed if present
$schema = __DIR__ . '/../schema.sql';
$seed = __DIR__ . '/../seed.sql';
if (file_exists($schema)) {
    $sql = file_get_contents($schema);
    $pdo->exec($sql);
}
if (file_exists($seed)) {
    $seedSql = file_get_contents($seed);
    $pdo->exec($seedSql);
}

// expose $pdo globally for tests and application includes that expect $pdo
global $pdo;
