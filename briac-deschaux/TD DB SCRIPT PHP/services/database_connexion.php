<?php
declare(strict_types=1);

/**
 * Database connection script
 *
 * Establishes a secure PDO connection to the MySQL database.
 * 
 * @author Briac Deschaux
 * @version 1.0
 */

$host = '195.15.235.20';
$dbname = 'briac_deschaux';
$username = 'root';
$password = 'INNnsk40374';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage()));
}
