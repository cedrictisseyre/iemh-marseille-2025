<?php
declare(strict_types=1);

/**
 * add_player.php
 *
 * Traite le formulaire d'ajout de joueur et redirige vers la page principale.
 * Utilise PDO et des requêtes préparées.
 */

require_once __DIR__ . '/../config/database_connexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../NFL_Stats_Analyzer.php?page=joueurs');
    exit;
}

// Récupération sécurisée des champs
$prenom = trim((string) ($_POST['prenom'] ?? ''));
$nom = trim((string) ($_POST['nom'] ?? ''));
$poste = trim((string) ($_POST['poste'] ?? ''));
$age = (int) ($_POST['age'] ?? 0);
$taille = (int) ($_POST['taille_cm'] ?? 0);
$poids = (int) ($_POST['poids_kg'] ?? 0);
$annee_debut = (int) ($_POST['annee_debut'] ?? 0);
$id_team = (int) ($_POST['id_team'] ?? 0);

if ($prenom === '' || $nom === '' || $id_team <= 0) {
    header('Location: ../NFL_Stats_Analyzer.php?page=joueurs&error=1');
    exit;
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO player (prenom, nom, poste, age, taille_cm, poids_kg, annee_debut, id_team)
         VALUES (:prenom, :nom, :poste, :age, :taille, :poids, :annee_debut, :id_team)'
    );
    $stmt->execute([
        ':prenom' => $prenom,
        ':nom' => $nom,
        ':poste' => $poste,
        ':age' => $age > 0 ? $age : null,
        ':taille' => $taille > 0 ? $taille : null,
        ':poids' => $poids > 0 ? $poids : null,
        ':annee_debut' => $annee_debut > 0 ? $annee_debut : null,
        ':id_team' => $id_team,
    ]);
} catch (PDOException $e) {
    // En production, logger l'erreur plutôt que d'afficher
    header('Location: ../NFL_Stats_Analyzer.php?page=joueurs&error=1');
    exit;
}

header('Location: ../NFL_Stats_Analyzer.php?page=joueurs&added=1');
exit;
