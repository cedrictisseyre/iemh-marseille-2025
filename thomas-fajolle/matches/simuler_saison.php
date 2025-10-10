<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Méthode non autorisée';
    exit;
}

try {
    $pdo->beginTransaction();

    // 1) Assurer la compétition "Ligue 1"
    $stmt = $pdo->prepare('SELECT id FROM competitions WHERE nom = :n LIMIT 1');
    $stmt->execute([':n' => 'Ligue 1']);
    $compId = $stmt->fetchColumn();
    if (!$compId) {
        $ins = $pdo->prepare('INSERT INTO competitions(nom) VALUES(:n)');
        $ins->execute([':n' => 'Ligue 1']);
        $compId = (int)$pdo->lastInsertId();
    } else {
        $compId = (int)$compId;
    }

    // 2) Supprimer tous les matchs de cette compétition
    $del = $pdo->prepare('DELETE FROM matches WHERE competition_id = :cid');
    $del->execute([':cid' => $compId]);

    // 3) Récupérer les équipes
    $teams = $pdo->query('SELECT id FROM teams ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
    if (count($teams) < 2) {
        throw new RuntimeException('Pas assez d\'équipes pour simuler une saison.');
    }

    // 4) Générer calendrier + scores aléatoires
    $start = new DateTime('2025-08-01 20:00:00');
    $offsetDays = 0;
    $ins = $pdo->prepare('INSERT INTO matches(date_match, competition_id, home_team_id, away_team_id, home_score, away_score) VALUES(?,?,?,?,?,?)');

    foreach ($teams as $homeId) {
        foreach ($teams as $awayId) {
            if ((int)$awayId === (int)$homeId) continue;
            $date = clone $start;
            $date->modify('+' . $offsetDays . ' days');
            $homeScore = random_int(0, 4);
            $awayScore = random_int(0, 4);
            $ins->execute([
                $date->format('Y-m-d H:i:s'),
                $compId,
                (int)$homeId,
                (int)$awayId,
                $homeScore,
                $awayScore
            ]);
            $offsetDays++;
        }
    }

    $pdo->commit();

    // Redirection vers la page des matchs avec un flag
    header('Location: ' . base_path('matches/matchs.php') . '?simule=1');
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo 'Erreur simulation: ' . htmlspecialchars($e->getMessage());
}
