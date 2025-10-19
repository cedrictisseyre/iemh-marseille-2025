<?php
declare(strict_types=1);

/**
 * database_connexion.php
 * Connexion PDO sécurisée et logging des erreurs (ne pas afficher en prod).
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
    // Log l'erreur (fichier) et renvoie un message générique
    $log = __DIR__ . '/../logs/db_errors.log';
    if (!is_dir(dirname($log))) {
        @mkdir(dirname($log), 0750, true);
    }
    error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, 3, $log);

    http_response_code(500);
    exit('Erreur de connexion à la base de données. Contacter l\'administrateur.');
}
