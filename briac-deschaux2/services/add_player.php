<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database_connexion.php';
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../NFL_Stats_Analyzer.php?page=joueurs');
    exit;
}

// CSRF
$token = $_POST['csrf_token'] ?? '';
if (!validate_csrf($token)) {
    header('Location: ../NFL_Stats_Analyzer.php?page=joueurs&error=csrf');
    exit;
}

// Récupération sécurisée des champs
$prenom = trim((string) ($_POST['prenom'] ?? ''));
$nom = trim((string) ($_POST['nom'] ?? ''));
$poste = trim((string) ($_POST['poste'] ?? '')); // code (ex: QB) ou texte
$position_id = isset($_POST['position_id']) && $_POST['position_id'] !== '' ? (int) $_POST['position_id'] : 0;
$age = isset($_POST['age']) && $_POST['age'] !== '' ? (int) $_POST['age'] : null;
$taille = isset($_POST['taille_cm']) && $_POST['taille_cm'] !== '' ? (int) $_POST['taille_cm'] : null;
$poids = isset($_POST['poids_kg']) && $_POST['poids_kg'] !== '' ? (int) $_POST['poids_kg'] : null;
$annee_debut = isset($_POST['annee_debut']) && $_POST['annee_debut'] !== '' ? (int) $_POST['annee_debut'] : null;
$id_team = isset($_POST['id_team']) ? (int) $_POST['id_team'] : 0;

if ($prenom === '' || $nom === '' || $id_team <= 0) {
    header('Location: ../NFL_Stats_Analyzer.php?page=joueurs&error=1');
    exit;
}

try {
    // Vérifier doublon "souple" : même nom + prénom + équipe
    $sqlCheck = "SELECT COUNT(*) FROM player WHERE nom = :nom AND prenom = :prenom AND id_team = :id_team";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':id_team' => $id_team,
    ]);
    $count = (int) $stmtCheck->fetchColumn();
    if ($count > 0) {
        header('Location: ../NFL_Stats_Analyzer.php?page=joueurs&error=doublon');
        exit;
    }

    $sql = 'INSERT INTO player (prenom, nom, poste, position_id, age, taille_cm, poids_kg, annee_debut, id_team)
            VALUES (:prenom, :nom, :poste, :position_id, :age, :taille, :poids, :annee_debut, :id_team)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':prenom' => $prenom,
        ':nom' => $nom,
        ':poste' => $poste !== '' ? $poste : null,
        ':position_id' => $position_id > 0 ? $position_id : null,
        ':age' => ($age !== null && $age > 0) ? $age : null,
        ':taille' => ($taille !== null && $taille > 0) ? $taille : null,
        ':poids' => ($poids !== null && $poids > 0) ? $poids : null,
        ':annee_debut' => ($annee_debut !== null && $annee_debut > 0) ? $annee_debut : null,
        ':id_team' => $id_team,
    ]);
} catch (PDOException $e) {
    app_log('add_player error: ' . $e->getMessage());
    header('Location: ../NFL_Stats_Analyzer.php?page=joueurs&error=1');
    exit;
}

header('Location: ../NFL_stats_analyzer.php?page=joueurs&added=1');
exit;
