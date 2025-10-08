<?php
declare(strict_types=1);

/**
 * database_connexion.php
 *
 * Établit une connexion PDO sécurisée vers la base de données.
 * Mettre à jour les paramètres $host, $dbname, $username, $password
 * avant de déployer.
 *
 * @author Briac Deschaux
 * @version 1.1
 */

$host = '195.15.235.20';
$dbname = 'briac_deschaux';
$username = 'root';
$password = 'INNnsk40374';

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

try {
    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // message protégé pour ne pas divulguer d'informations sensibles
    http_response_code(500);
    exit('Erreur de connexion à la base de données. Voir l\'administrateur.');
}
