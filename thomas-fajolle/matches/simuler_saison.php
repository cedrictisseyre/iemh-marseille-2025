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
    $n = count($teams);
    if ($n < 2) {
        throw new RuntimeException('Pas assez d\'équipes pour simuler une saison.');
    }
    if ($n % 2 !== 0) {
        // Pour rester simple, on refuse l\'impair ici
        throw new RuntimeException('Nombre d\'équipes impair: la génération aller-retour exige un nombre pair.');
    }

    // 4) Générer un vrai calendrier aller-retour par "journées" (méthode du cercle)
    $rounds = [];
    $fixed = (int)$teams[0];
    $rot = array_map('intval', array_slice($teams, 1));
    $half = $n / 2;
    $totalRounds = $n - 1; // 17 si 18 équipes

    for ($r = 0; $r < $totalRounds; $r++) {
        $full = array_merge([$fixed], $rot);
        $pairs = [];
        for ($i = 0; $i < $half; $i++) {
            $t1 = (int)$full[$i];
            $t2 = (int)$full[$n - 1 - $i];
            // alterner domicile/extérieur pour équilibrer
            if ($r % 2 === 0) {
                $pairs[] = [$t1, $t2];
            } else {
                $pairs[] = [$t2, $t1];
            }
        }
        $rounds[] = $pairs; // une "journée" = 9 matchs pour 18 équipes
        // rotation: dernier du rot vient en tête, et l\'ancien premier du rot va à la place du fixed voisin
        $last = array_pop($rot);
        array_unshift($rot, $last);
    }

    // Retour (aller-retour): dupliquer en inversant domicile/extérieur
    $returnRounds = [];
    foreach ($rounds as $pairs) {
        $list = [];
        foreach ($pairs as [$h, $a]) {
            $list[] = [$a, $h];
        }
        $returnRounds[] = $list;
    }

    // 5) Affecter des dates: 1 week-end par journée
    $firstWeekend = new DateTime('2025-08-02 17:00:00'); // samedi 17:00 de départ
    $timeSlotsSat = ['14:00', '17:00', '19:00', '20:45', '21:00'];
    $timeSlotsSun = ['13:00', '15:00', '17:00', '21:00'];

    $ins = $pdo->prepare('INSERT INTO matches(date_match, competition_id, home_team_id, away_team_id, home_score, away_score) VALUES(?,?,?,?,?,?)');

    $roundIndex = 0;
    $allRounds = array_merge($rounds, $returnRounds); // 34 journées
    foreach ($allRounds as $pairs) {
        // Calculer les datetimes du weekend courant
        $weekStart = (clone $firstWeekend)->modify('+' . $roundIndex . ' week');
        $sat = (clone $weekStart)->modify('Saturday this week');
        $sun = (clone $weekStart)->modify('Sunday this week');

        $dates = [];
        // 5 matchs le samedi (timeslots samedi), 4 le dimanche (timeslots dimanche)
        foreach ($timeSlotsSat as $ts) {
            [$h, $m] = explode(':', $ts);
            $d = (clone $sat)->setTime((int)$h, (int)$m, 0);
            $dates[] = $d;
        }
        foreach ($timeSlotsSun as $ts) {
            [$h, $m] = explode(':', $ts);
            $d = (clone $sun)->setTime((int)$h, (int)$m, 0);
            $dates[] = $d;
        }

        // Insérer les 9 matchs de la journée
        $i = 0;
        foreach ($pairs as [$homeId, $awayId]) {
            if ($i >= 9) break; // sécurité
            $date = $dates[$i];
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
            $i++;
        }
        $roundIndex++;
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
