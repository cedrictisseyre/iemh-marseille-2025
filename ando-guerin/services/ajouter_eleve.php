<?php
require_once '../connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = $_POST['prenom'] ?? '';
    $nom = $_POST['nom'] ?? '';
    if ($prenom && $nom) {
        $conn->prepare('INSERT INTO eleves (prenom, nom) VALUES (?, ?)')->execute([$prenom, $nom]);
        echo 'Élève ajouté avec succès !';
    } else {
        echo 'Tous les champs sont obligatoires.';
    }
}
?>