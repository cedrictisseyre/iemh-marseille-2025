<?php

// Bootstrap for PHPUnit tests: create a temporary SQLite database for isolation.
// Mark that we are running under tests so includes can avoid sending headers / starting sessions
if (!defined('IN_TEST')) {
    define('IN_TEST', true);
}
$dbFile = __DIR__ . '/../var/test_db.sqlite';
@mkdir(dirname($dbFile), 0777, true);
if (file_exists($dbFile)) {
    unlink($dbFile);
}
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// expose $pdo globally so includes that inspect $GLOBALS['pdo'] can use it
$GLOBALS['pdo'] = $pdo;

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

// Start PHP built-in server for integration tests if not already running
$host = '127.0.0.1';
$port = 8000;
$varDir = __DIR__ . '/../var';
$pidFile = $varDir . '/test_server.pid';
$logFile = $varDir . '/test_server.log';
@mkdir($varDir, 0777, true);
if (!file_exists($pidFile)) {
    $docroot = realpath(__DIR__ . '/..');
    // start server logging to var/test_server.log so CI can inspect it
    $cmd = sprintf("php -S %s:%d -t %s > %s 2>&1 & echo $!", $host, $port, $docroot, $logFile);
    $output = [];
    exec($cmd, $output);
    if (count($output)) {
        $pid = (int)$output[0];
        file_put_contents($pidFile, $pid);
        // wait for server to accept connections (short timeout)
        $maxWait = 5; // seconds
        $started = false;
        for ($i = 0; $i < $maxWait * 10; $i++) {
            // try connecting
            $fp = @fsockopen($host, $port, $errno, $errstr, 0.1);
            if ($fp) {
                fclose($fp);
                $started = true;
                break;
            }
            usleep(100000);
        }
        if (! $started) {
            // leave logs for debugging but continue; tests may fail if server not started
        }
    }
}

// Register shutdown to stop the server when PHP process ends
register_shutdown_function(function () use ($pidFile) {
    if (!file_exists($pidFile)) {
        return;
    }
    $pid = (int)@file_get_contents($pidFile);
    if ($pid > 0) {
        // try to kill the process
        @exec('kill ' . $pid . ' 2>/dev/null');
        @unlink($pidFile);
    }
});
