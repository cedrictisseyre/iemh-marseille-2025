<?php
// Connexion à la base de données
$host = '195.15.235.20';
$user = 'root'; // à adapter selon votre config
$password = 'INNnsk40374';    // à adapter selon votre config
$db = 'chloe_desbordes';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db"; $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
?>
