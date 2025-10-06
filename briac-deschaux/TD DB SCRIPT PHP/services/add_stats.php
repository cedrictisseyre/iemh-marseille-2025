<?php
declare(strict_types=1);

/**
 * add_stats.php
 *
 * Traite l'ajout de statistiques pour un joueur (saison courante).
 * Utilise PDO, validations basiques et redirection.
 */

require_once __DIR__ . '/../config/database_connexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../NFL_Stats_Analyzer.php?page=stats');
    exit;
}

$id_player = (int) ($_POST['id_player'] ?? 0);
$saison = (int) date('Y');

// conversions simples
$yards_passe = (int) ($_POST['yards_passe'] ?? 0);
$td_passe = (int) ($_POST['td_passe'] ?? 0);
$interceptions = (int) ($_POST['interceptions'] ?? 0);
$yards_course = (int) ($_POST['yards_course'] ?? 0);
$td_course = (int) ($_POST['td_course'] ?? 0);
$receptions = (int) ($_POST['receptions'] ?? 0);
$yards_reception = (int) ($_POST['yards_reception'] ?? 0);
$td_reception = (int) ($_POST['td_reception'] ?? 0);
$plaquages = (int) ($_POST['plaquages'] ?? 0);
$sacks = (float) ($_POST['sacks'] ?? 0.0);
$interceptions_def = (int) ($_POST['interceptions_def'] ?? 0);
$fg_reussis = (int) ($_POST['fg_reussis'] ?? 0);
$punts = (int) ($_POST['punts'] ?? 0);

if ($id_player <= 0) {
    header('Location: ../NFL_Stats_Analyzer.php?page=stats&error=1');
    exit;
}

try {
    $sql = 'INSERT INTO stats
        (id_player, saison, yards_passe, td_passe, interceptions, yards_course, td_course, receptions, yards_reception, td_reception, plaquages, sacks, interceptions_def, fg_reussis, punts)
        VALUES
        (:id_player, :saison, :yards_passe, :td_passe, :interceptions, :yards_course, :td_course, :receptions, :yards_reception, :td_reception, :plaquages, :sacks, :interceptions_def, :fg_reussis, :punts)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_player' => $id_player,
        ':saison' => $saison,
        ':yards_passe' => $yards_passe,
        ':td_passe' => $td_passe,
        ':interceptions' => $interceptions,
        ':yards_course' => $yards_course,
        ':td_course' => $td_course,
        ':receptions' => $receptions,
        ':yards_reception' => $yards_reception,
        ':td_reception' => $td_reception,
        ':plaquages' => $plaquages,
        ':sacks' => $sacks,
        ':interceptions_def' => $interceptions_def,
        ':fg_reussis' => $fg_reussis,
        ':punts' => $punts,
    ]);
} catch (PDOException $e) {
    header('Location: ../NFL_Stats_Analyzer.php?page=stats&error=1');
    exit;
}

header('Location: ../NFL_Stats_Analyzer.php?page=stats&added=1');
exit;
