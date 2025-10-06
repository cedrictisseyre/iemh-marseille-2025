<?php
$host = 'localhost';
$db   = 'nicolas_jaulin'; // à confirmer dans la liste des bases
$user = 'root';
$pass = '<?php
$host = 'localhost';
$db   = 'nicolas_jaulin'; // à confirmer dans la liste des bases
$user = 'root';
$pass = ''; // à compléter si tu as défini un mot de passe
$charset = 'INNnsk40374'; // à compléter si tu as défini un mot de passe
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
