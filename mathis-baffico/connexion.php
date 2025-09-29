<?php
// connexion.php : fichier unique pour se connecter à la BDD
$host = 'localhost'; 
$dbname = 'Mathis_Baffico'; 
$username = 'root'; 
$password = 'INNnsk40374'; // ⚠️ à remplacer par ton vrai mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussie !"; // Décommente si tu veux tester
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>