<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'database_connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $poste = trim($_POST['poste'] ?? '');
    $age = (int) ($_POST['age'] ?? 0);
    $taille = (int) ($_POST['taille_cm'] ?? 0);
    $poids = (int) ($_POST['poids_kg'] ?? 0);
    $annee_debut = (int) ($_POST['annee_debut'] ?? 0);
    $id_team = (int) ($_POST['id_team'] ?? 0);

    if ($prenom && $nom && $id_team) {
        $stmt = $pdo->prepare("
            INSERT INTO player (prenom, nom, poste, age, taille_cm, poids_kg, annee_debut, id_team)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$prenom, $nom, $poste, $age, $taille, $poids, $annee_debut, $id_team]);
    }
}

header("Location: NFL_Stats_Analyzer.php?page=joueurs&added=1");
exit;

