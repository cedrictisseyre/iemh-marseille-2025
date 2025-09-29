<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Informations de connexion
$host = 'localhost';      // ou l'adresse du serveur MySQL
$dbname = 'francois_duclos'; 
$username = 'root'; 
$password = 'INNnsk40374';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie !<br>";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
