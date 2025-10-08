<?php
/**
 * Exemple de fichier de connexion pour le Dev Container
 * 
 * Utilisez ce fichier comme référence pour vous connecter à la base de données
 * dans l'environnement de développement local.
 * 
 * Pour le Dev Container, utilisez les paramètres suivants :
 */

// Configuration pour le Dev Container
$host = 'mysql';  // Nom du service MySQL dans docker-compose
$user = 'root';
$password = 'root_password';
$dbname = 'iemh_dev';  // Base de données par défaut, ou créez la vôtre dans phpMyAdmin

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie à la base de données !<br>";
    echo "Hôte : $host<br>";
    echo "Base de données : $dbname<br>";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage();
    exit;
}

/**
 * Pour le déploiement en production, utilisez vos paramètres habituels :
 * 
 * $host = '195.15.235.20';
 * $user = 'root';
 * $password = 'INNnsk40374';
 * $dbname = 'votre_nom_base';
 */
?>
