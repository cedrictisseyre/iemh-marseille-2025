<?php
// Connexion à la base de données MySQL

$host = '195.15.235.20';
$user = 'root';
$password = 'INNnsk40374';
$dbname = 'cedric_tisseyre'; // Ajoute le nom de la base

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

// ... Utilisez $conn pour vos requêtes SQL ...
?>
