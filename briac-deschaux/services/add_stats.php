<?php
declare(strict_types=1);

/**
 * services/add_stats.php
 * Ajout sécurisé de statistiques. Vérifie unicité (id_player, saison).
 */

require_once __DIR__ . '/../config/database_connexion.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../NFL_Stats_Analyzer.php?page=stats');
    exit;
}

// CSRF
$token = $_POST['csrf_token'] ?? '';
if (!validate_csrf($token)) {
    header('Location: ../NFL_Stats_Analyzer.php?page=stats&error=csrf');
    exit;
}

$id_player = (int) ($_POST['id_player'] ?? 0);
$saison = (int) date('Y');

// conversions simples (autorise null si champs vides)
$yards_passe = isset($_POST['yards_passe']) && $_POST['yards_passe'] !== '' ? (int) $_POST['yards_passe'] : null;
$td_passe = isset($_POST['td_passe']) && $_POST['td_passe'] !== '' ? (int) $_POST['td_passe'] : null;
$interceptions = isset($_POST['interceptions']) && $_POST['interceptions'] !== '' ? (int) $_POST['interceptions'] : null;
$yards_course = isset($_POST['yards_course']) && $_POST['yards_course'] !== '' ? (int) $_POST['yards_course'] : null;
$td_course = isset($_POST['td_course']) && $_POST['td_course'] !== '' ? (int) $_POST['td_course'] : null;
$receptions = isset($_POST['receptions']) && $_POST['receptions'] !== '' ? (int) $_POST['receptions'] : null;
$yards_reception = isset($_POST['yards_reception']) && $_POST['yards_reception'] !== '' ? (int) $_POST['yards_reception'] : null;
$td_reception = isset($_POST['td_reception']) && $_POST['td_reception'] !== '' ? (int) $_POST['td_reception'] : null;
$plaquages = isset($_POST['plaquages']) && $_POST['plaquages'] !== '' ? (int) $_POST['plaquages'] : null;
$sacks = isset($_POST['sacks']) && $_POST['sacks'] !== '' ? (float) $_POST['sacks'] : null;
$interceptions_def = isset($_POST['interceptions_def']) && $_POST['interceptions_def'] !== '' ? (int) $_POST['interceptions_def'] : null;
$fg_reussis = isset($_POST['fg_reussis']) && $_POST['fg_reussis'] !== '' ? (int) $_POST['fg_reussis'] : null;
$punts = isset($_POST['punts']) && $_POST['punts'] !== '' ? (int) $_POST['punts'] : null;

if ($id_player <= 0) {
    header('Location: ../NFL_Stats_Analyzer.php?page=stats&error=1');
    exit;
}

try {
    // Vérifier unicité (id_player, saison)
    $chk = $pdo->prepare('SELECT COUNT(*) FROM stats WHERE id_player = ? AND saison = ?');
    $chk->execute([$id_player, $saison]);
    if ((int)$chk->fetchColumn() > 0) {
        header('Location: ../NFL_Stats_Analyzer.php?page=stats&error=duplicate');
        exit;
    }

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
    app_log('add_stats error: ' . $e->getMessage());
    header('Location: ../NFL_Stats_Analyzer.php?page=stats&error=1');
    exit;
}

header('Location: ../NFL_Stats_Analyzer.php?page=stats&added=1');
exit;
