<?php
$host = '195.15.235.20';
$user = 'root'; // remplacer par un utilisateur non-root en prod
$password = 'INNnsk40374';
$dbname = 'Andoni_guerin';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    error_log('Erreur BDD : ' . $e->getMessage());
    http_response_code(500);
    echo 'Erreur de connexion à la base de données.';
    exit;
}
