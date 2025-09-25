<?php
// Connexion à la base de données
$host = 'localhost';
$user = 'root'; // à adapter selon votre config
$pass = '';    // à adapter selon votre config
$db = 'chloe_desbordes';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db; $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
?>
