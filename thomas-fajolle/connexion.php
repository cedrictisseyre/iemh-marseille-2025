<?php
require_once __DIR__ . '/config.php'; // inclut les paramètres globaux

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    // echo "Connexion réussie"; // à activer pour tester
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

