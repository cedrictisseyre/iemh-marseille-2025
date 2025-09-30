<?php
$host = '195.15.235.20';
$user = 'root';
$password = 'INNnsk40374'; // ou ton propre mot de passe
$dbname = 'coline_pontal';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Ne pas interrompre l'exécution : on conserve le message d'erreur
    $pdo = null;
    $db_error_msg = 'Erreur de connexion à la base de données : ' . $e->getMessage();
    error_log($db_error_msg);
}