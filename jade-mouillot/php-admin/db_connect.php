<?php
$host = '195.15.235.20';
$db   = 'jade_mouillot';
$user = 'TON_UTILISATEUR'; // À remplacer
$pass = 'TON_MOT_DE_PASSE'; // À remplacer
$charset = 'utf8mb3';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
