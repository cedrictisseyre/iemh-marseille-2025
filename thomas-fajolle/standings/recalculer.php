<?php
// Script de recalcul complet du classement à partir des résultats des matchs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connexion.php';

// Sécurité basique : uniquement via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: classement.php');
    exit;
}

try {
    // 1. Récupération des équipes
    $stmtTeams = $pdo->query("SELECT id FROM teams");
    $teams = $stmtTeams->fetchAll(PDO::FETCH_COLUMN);
    if (!$teams) {
        header('Location: classement.php?m=' . urlencode('Aucune équipe trouvée'));
        exit;
    }

    // 2. Déterminer si une compétition "Ligue 1" existe (sinon, on prend tous les matchs)
    $stmtComp = $pdo->prepare("SELECT id FROM competitions WHERE nom = :n LIMIT 1");
    $stmtComp->execute([':n' => 'Ligue 1']);
    $ligue1Id = $stmtComp->fetchColumn();

    // 3. Initialiser les stats
    $stats = [];
    foreach ($teams as $tid) {
        $stats[$tid] = [
            'played' => 0,
            'won' => 0,
            'draw' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0
        ];
    }

    // 4. Récupérer les matchs joués (scores non nuls)
    $sqlMatches = "SELECT home_team_id, away_team_id, home_score, away_score, competition_id
                   FROM matches
                   WHERE home_score IS NOT NULL AND away_score IS NOT NULL";
    $params = [];
    if ($ligue1Id) {
        $sqlMatches .= " AND competition_id = :cid";
        $params[':cid'] = $ligue1Id;
    }
    $stmtMatches = $pdo->prepare($sqlMatches);
    $stmtMatches->execute($params);
    $matches = $stmtMatches->fetchAll(PDO::FETCH_ASSOC);

    // 5. Calcul des stats
    foreach ($matches as $m) {
        $home = (int)$m['home_team_id'];
        $away = (int)$m['away_team_id'];
        $hs = (int)$m['home_score'];
        $as = (int)$m['away_score'];

        if (!isset($stats[$home]) || !isset($stats[$away])) continue; // robustesse

        // Joués
        $stats[$home]['played']++;
        $stats[$away]['played']++;

        // Buts
        $stats[$home]['goals_for'] += $hs;
        $stats[$home]['goals_against'] += $as;
        $stats[$away]['goals_for'] += $as;
        $stats[$away]['goals_against'] += $hs;

        // Résultat
        if ($hs > $as) {
            $stats[$home]['won']++;
            $stats[$away]['lost']++;
        } elseif ($hs < $as) {
            $stats[$away]['won']++;
            $stats[$home]['lost']++;
        } else { // nul
            $stats[$home]['draw']++;
            $stats[$away]['draw']++;
        }
    }

    // 6. Écrire dans la table standings (reset puis insert)
    $pdo->beginTransaction();
    // Utiliser DELETE plutôt que TRUNCATE (droits / FK)
    $pdo->exec("DELETE FROM standings");

    $ins = $pdo->prepare("INSERT INTO standings
        (team_id, played, won, draw, lost, goals_for, goals_against, goal_difference, points)
        VALUES (:team_id, :played, :won, :drawn, :lost, :gf, :ga, :gd, :pts)");

    foreach ($stats as $teamId => $st) {
        $won  = $st['won'];
        $draw = $st['draw'];
        $lost = $st['lost'];
        $gf   = $st['goals_for'];
        $ga   = $st['goals_against'];
        $played = $st['played'];
        $gd   = $gf - $ga;
        $points = $won * 3 + $draw; // 3-1-0 classique

        $ins->execute([
            ':team_id' => $teamId,
            ':played' => $played,
            ':won' => $won,
            ':drawn' => $draw,
            ':lost' => $lost,
            ':gf' => $gf,
            ':ga' => $ga,
            ':gd' => $gd,
            ':pts' => $points,
        ]);
    }

    $pdo->commit();

    header('Location: classement.php?m=' . urlencode('Classement recalculé avec succès'));
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: classement.php?m=' . urlencode('Erreur recalcul: ' . $e->getMessage()));
    exit;
}
