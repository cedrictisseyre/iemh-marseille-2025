<?php
require __DIR__ . '/francoisdcls/tests/bootstrap.php';
if (isset($pdo) && $pdo instanceof PDO) {
    echo "PDO OK\n";
} else {
    echo "PDO MISSING\n";
}
echo file_exists(__DIR__ . '/francoisdcls/var/test_db.sqlite') ? "DBFILE OK\n" : "DBFILE MISSING\n";
echo file_exists(__DIR__ . '/francoisdcls/var/test_server.pid') ? "PIDFILE: " . file_get_contents(__DIR__ . '/francoisdcls/var/test_server.pid') . "\n" : "PIDFILE MISSING\n";
