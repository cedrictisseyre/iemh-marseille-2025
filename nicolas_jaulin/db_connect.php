
<?php
$host = 'localhost';
$db   = 'nicolas_jaulin';
$user = 'root';
$pass = '';
$host = '195.15.235.20';
$db   = 'nicolas_jaulin';
$user = 'root';
$pass = 'INNnsk40374';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
     throw new PDOException($e->getMessage(), (int)$e->getCode());
}
