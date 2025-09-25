<?php
// Connexion à la base de données MySQL
$host = '195.15.235.20';
$user = 'root';
$password = 'INNnsk40374';
$dbname = 'maelle_goujat';
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
?>
