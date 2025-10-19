<?php
// Connexion PDO sécurisée
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=briac_deschaux;charset=utf8',
        'root', // ou ton utilisateur
        '',     // ton mot de passe
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . htmlspecialchars($e->getMessage()));
}
?>
