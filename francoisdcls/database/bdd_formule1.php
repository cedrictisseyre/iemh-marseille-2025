<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Informations de connexion
$host = '195.15.235.20';      // ou l'adresse du serveur MySQL
$dbname = 'francois_duclos';
$username = 'root';
$password = 'INNnsk40374';

// Respect existing $pdo (tests may provide a SQLite PDO via bootstrap)
if (!isset($pdo) || !$pdo) {
    // If a test SQLite DB exists (created by tests/bootstrap.php), prefer it so the
    // built-in PHP server used during tests connects to the same SQLite file.
    $sqliteFile = __DIR__ . '/../var/test_db.sqlite';
    if (file_exists($sqliteFile)) {
        try {
            $pdo = new PDO('sqlite:' . $sqliteFile);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erreur de connexion SQLite : " . $e->getMessage();
        }
    } else {
        try {
            // Connexion à la base de données MySQL
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connexion réussie !<br>";
        } catch (PDOException $e) {
            echo "Erreur de connexion MySQL : " . $e->getMessage();
        }
    }
}
