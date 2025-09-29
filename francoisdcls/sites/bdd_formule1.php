<?php
// Fichier de connexion PDO à la base de données MySQL pour les projets F1
$host = 'localhost';
$dbname = 'francoisdcls';
$username = 'root';
$password = 'INNnsk40374';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
