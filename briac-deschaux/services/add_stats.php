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
function nullableInt(string $key): ?int {
    return (isset($_POST[$key]) && $_POST[$key] !== '') ? (int) $_POST[$key] : null;
}
function nullableFloat(string $key): ?float {
    return (isset($_POST[$key]) && $_POST[$key] !== '') ? (float) $_POST[$key] : null;
}

$yards_passe         = nullableInt('yards_passe');
$td_passe            = nullableInt('td_passe');
$interceptions       = nullableInt('interceptions');
$yards_course        = nullableInt('yards_course');
$td_course           = nullableInt('td_course');
$receptions          = nullableInt('receptions');
$yards_reception     = nullableInt('yards_reception');
$td_reception        = nullableInt('td_reception');
$plaquages           = nullableInt('plaquages');
$sacks               = nullableFloat('sacks');
$interceptions_def   = nullableInt('interceptions_def');
$fg_reussis          = nullableInt('fg_reussis');
$punts               = nullableInt('punts');

// nouvelles stats special teams
$field_goals_made        = nullableInt('field_goals_made');
$field_goals_attempted   = nullableInt('field_goals_attempted');
$extra_points_made       = nullableInt('extra_points_made');
$extra_points_attempted  = nullableInt('extra_points_attempted');
$punt_yards              = nullableInt('punt_yards');
$longest_punt            = nullableInt('longest_punt');
$inside_20               = nullableInt('inside_20');

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
        (id_player, saison, yards_passe, td_passe, interceptions, yards_course, td_course,
         receptions, yards_reception, td_reception, plaquages, sacks, interceptions_def,
         fg_reussis, punts, field_goals_made, field_goals_attempted, extra_points_made,
         extra_points_attempted, punt_yards, longest_punt, inside_20)
        VALUES
        (:id_player, :saison, :yards_passe, :td_passe, :interceptions, :yards_course, :td_course,
         :receptions, :yards_reception, :td_reception, :plaquages, :sacks, :interceptions_def,
         :fg_reussis, :punts, :field_goals_made, :field_goals_attempted, :extra_points_made,
         :extra_points_attempted, :punt_yards, :longest_punt, :inside_20)';

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
        ':field_goals_made' => $field_goals_made,
        ':field_goals_attempted' => $field_goals_attempted,
        ':extra_points_made' => $extra_points_made,
        ':extra_points_attempted' => $extra_points_attempted,
        ':punt_yards' => $punt_yards,
        ':longest_punt' => $longest_punt,
        ':inside_20' => $inside_20,
    ]);
} catch (PDOException $e) {
    app_log('add_stats error: ' . $e->getMessage());
    header('Location: ../NFL_Stats_Analyzer.php?page=stats&error=1');
    exit;
}

header('Location: ../NFL_Stats_Analyzer.php?page=stats&added=1');
exit;
