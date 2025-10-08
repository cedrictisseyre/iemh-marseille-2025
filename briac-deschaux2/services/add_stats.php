<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database_connexion.php';
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$id_player = isset($_POST['id_player']) ? (int) $_POST['id_player'] : 0;
$saison = (int) date('Y');

// Récupérations (noms identiques aux champs du formulaire / colonnes)
$passing_yards = isset($_POST['passing_yards']) && $_POST['passing_yards'] !== '' ? (int) $_POST['passing_yards'] : null;
$passing_tds = isset($_POST['passing_tds']) && $_POST['passing_tds'] !== '' ? (int) $_POST['passing_tds'] : null;
$interceptions = isset($_POST['interceptions']) && $_POST['interceptions'] !== '' ? (int) $_POST['interceptions'] : null;
$rushing_yards = isset($_POST['rushing_yards']) && $_POST['rushing_yards'] !== '' ? (int) $_POST['rushing_yards'] : null;
$rushing_tds = isset($_POST['rushing_tds']) && $_POST['rushing_tds'] !== '' ? (int) $_POST['rushing_tds'] : null;
$receptions = isset($_POST['receptions']) && $_POST['receptions'] !== '' ? (int) $_POST['receptions'] : null;
$receiving_yards = isset($_POST['receiving_yards']) && $_POST['receiving_yards'] !== '' ? (int) $_POST['receiving_yards'] : null;
$receiving_tds = isset($_POST['receiving_tds']) && $_POST['receiving_tds'] !== '' ? (int) $_POST['receiving_tds'] : null;
$tackles = isset($_POST['tackles']) && $_POST['tackles'] !== '' ? (int) $_POST['tackles'] : null;
$sacks = isset($_POST['sacks']) && $_POST['sacks'] !== '' ? (float) $_POST['sacks'] : null;
$interceptions_def = isset($_POST['interceptions_def']) && $_POST['interceptions_def'] !== '' ? (int) $_POST['interceptions_def'] : null;

$field_goals_made = isset($_POST['field_goals_made']) && $_POST['field_goals_made'] !== '' ? (int) $_POST['field_goals_made'] : null;
$field_goals_attempted = isset($_POST['field_goals_attempted']) && $_POST['field_goals_attempted'] !== '' ? (int) $_POST['field_goals_attempted'] : null;
$extra_points_made = isset($_POST['extra_points_made']) && $_POST['extra_points_made'] !== '' ? (int) $_POST['extra_points_made'] : null;
$extra_points_attempted = isset($_POST['extra_points_attempted']) && $_POST['extra_points_attempted'] !== '' ? (int) $_POST['extra_points_attempted'] : null;

$punts = isset($_POST['punts']) && $_POST['punts'] !== '' ? (int) $_POST['punts'] : null;
$punt_yards = isset($_POST['punt_yards']) && $_POST['punt_yards'] !== '' ? (int) $_POST['punt_yards'] : null;
$longest_punt = isset($_POST['longest_punt']) && $_POST['longest_punt'] !== '' ? (int) $_POST['longest_punt'] : null;
$inside_20 = isset($_POST['inside_20']) && $_POST['inside_20'] !== '' ? (int) $_POST['inside_20'] : null;

if ($id_player <= 0) {
    header('Location: ../NFL_Stats_Analyzer.php?page=stats&error=1');
    exit;
}

try {
    // Unicité (id_player, saison)
    $chk = $pdo->prepare('SELECT COUNT(*) FROM stats WHERE id_player = ? AND saison = ?');
    $chk->execute([$id_player, $saison]);
    if ((int)$chk->fetchColumn() > 0) {
        header('Location: ../NFL_Stats_Analyzer.php?page=stats&error=duplicate');
        exit;
    }

    $sql = 'INSERT INTO stats
        (id_player, saison,
         passing_yards, passing_tds, interceptions,
         rushing_yards, rushing_tds,
         receptions, receiving_yards, receiving_tds,
         tackles, sacks, interceptions_def,
         field_goals_made, field_goals_attempted,
         extra_points_made, extra_points_attempted,
         punts, punt_yards, longest_punt, inside_20)
        VALUES
        (:id_player, :saison,
         :passing_yards, :passing_tds, :interceptions,
         :rushing_yards, :rushing_tds,
         :receptions, :receiving_yards, :receiving_tds,
         :tackles, :sacks, :interceptions_def,
         :field_goals_made, :field_goals_attempted,
         :extra_points_made, :extra_points_attempted,
         :punts, :punt_yards, :longest_punt, :inside_20)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_player' => $id_player,
        ':saison' => $saison,
        ':passing_yards' => $passing_yards,
        ':passing_tds' => $passing_tds,
        ':interceptions' => $interceptions,
        ':rushing_yards' => $rushing_yards,
        ':rushing_tds' => $rushing_tds,
        ':receptions' => $receptions,
        ':receiving_yards' => $receiving_yards,
        ':receiving_tds' => $receiving_tds,
        ':tackles' => $tackles,
        ':sacks' => $sacks,
        ':interceptions_def' => $interceptions_def,
        ':field_goals_made' => $field_goals_made,
        ':field_goals_attempted' => $field_goals_attempted,
        ':extra_points_made' => $extra_points_made,
        ':extra_points_attempted' => $extra_points_attempted,
        ':punts' => $punts,
        ':punt_yards' => $punt_yards,
        ':longest_punt' => $longest_punt,
        ':inside_20' => $inside_20,
    ]);
} catch (PDOException $e) {
    app_log('add_stats error: ' . $e->getMessage());
    header('Location: ../NFL_Stats_Analyzer.php?page=stats&error=1');
    exit;
}

header('Location: ../NFL_stats_analyzer.php?page=stats&added=1');
exit;
