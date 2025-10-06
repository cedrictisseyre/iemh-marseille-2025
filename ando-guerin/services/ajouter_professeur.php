<?php
require_once '../connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = $_POST['prenom'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $matiere = $_POST['matiere'] ?? '';

    if ($prenom && $nom && $matiere) {
        // Vérifier si la matière existe déjà
        $stmt = $conn->prepare('SELECT id FROM matieres WHERE nom = ?');
        $stmt->execute([$matiere]);
        $matiere_id = $stmt->fetchColumn();
        if (!$matiere_id) {
            $conn->prepare('INSERT INTO matieres (nom) VALUES (?)')->execute([$matiere]);
            $matiere_id = $conn->lastInsertId();
        }
        // Ajouter le professeur
        $conn->prepare('INSERT INTO professeurs (prenom, nom) VALUES (?, ?)')->execute([$prenom, $nom]);
        $prof_id = $conn->lastInsertId();
        // Lier professeur et matière
        $conn->prepare('INSERT INTO professeurs_matieres (professeur_id, matiere_id) VALUES (?, ?)')->execute([$prof_id, $matiere_id]);
        echo 'Professeur ajouté avec succès !';
    } else {
        echo 'Tous les champs sont obligatoires.';
    }
}
?>