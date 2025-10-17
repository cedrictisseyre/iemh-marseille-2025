<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Informations de connexion (valeurs par défaut, peuvent être overridées via env)
$host = getenv('FRANCOISDB_HOST') ?: '195.15.235.20';
$dbname = getenv('FRANCOISDB_NAME') ?: 'francois_duclos';
$username = getenv('FRANCOISDB_USER') ?: 'root';
$password = getenv('FRANCOISDB_PASS') ?: 'INNnsk40374';

// Driver override via environment variable: 'sqlite' or 'mysql'. Si non défini,
// comportement précédent : utiliser SQLite si le fichier de test existe, sinon MySQL.
$driver = getenv('FRANCOISDB_DRIVER') ?: null;

// Respect existing $pdo (tests may provide a SQLite PDO via bootstrap).
// Some test bootstrap code may set $pdo in the global scope; prefer that.
if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
    $pdo = $GLOBALS['pdo'];
} elseif (!isset($pdo) || !$pdo) {
    $sqliteFile = __DIR__ . '/../var/test_db.sqlite';

    // Helper to connect to sqlite
    $connectSqlite = function () use ($sqliteFile) {
        try {
            $pdoLocal = new PDO('sqlite:' . $sqliteFile);
            $pdoLocal->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdoLocal;
        } catch (PDOException $e) {
            echo "Erreur de connexion SQLite : " . $e->getMessage();
            return null;
        }
    };

    // Helper to connect to MySQL
    $connectMysql = function () use ($host, $dbname, $username, $password) {
        try {
            $pdoLocal = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdoLocal->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdoLocal;
        } catch (PDOException $e) {
            echo "Erreur de connexion MySQL : " . $e->getMessage();
            return null;
        }
    };

    if ($driver === 'sqlite') {
        // Force sqlite
        if (file_exists($sqliteFile)) {
            $pdo = $connectSqlite();
        } else {
            echo "FRANCOISDB_DRIVER=sqlite mais le fichier $sqliteFile est introuvable.\n";
            $pdo = null;
        }
    } elseif ($driver === 'mysql') {
        // Force mysql
        $pdo = $connectMysql();
    } else {
        // Comportement par défaut modifié : privilégier MySQL si possible,
        // mais retomber sur SQLite si la connexion MySQL échoue et que
        // le fichier de test existe.
        $pdo = $connectMysql();
        if ($pdo === null && file_exists($sqliteFile)) {
            // MySQL non disponible, utiliser SQLite local si présent
            $pdo = $connectSqlite();
        }
    }
}

// Expose a get_pdo() helper for consumers that include this file directly.
    if (!function_exists('get_pdo')) {
    /**
     * Return the shared PDO instance or null.
     * @return \PDO|null
     */
    function get_pdo(): ?\PDO
    {
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof \PDO) {
            return $GLOBALS['pdo'];
        }
        global $pdo;
        if (isset($pdo) && $pdo instanceof \PDO) {
            return $pdo;
        }
        return null;
    }
}
