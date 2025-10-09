<?php
/**
 * Script d'import des joueurs de la saison 2024-2025 à partir d'un CSV.
 * 1. Crée les tables seasons et player_seasons si elles n'existent pas.
 * 2. Crée la saison 2024-2025 si absente.
 * 3. Parcourt le CSV players/data/ligue1_2024_2025_players.csv
 * 4. Pour chaque ligne: crée le joueur s'il n'existe pas (match nom+prenom), puis insère liaison saison/équipe.
 *
 * A exécuter depuis le navigateur ou en CLI (php import_2024_2025.php)
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connexion.php';

function ensureSchema(PDO $pdo) {
    // seasons
    $pdo->exec("CREATE TABLE IF NOT EXISTS seasons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) UNIQUE NOT NULL,
        start_date DATE NULL,
        end_date DATE NULL,
        active TINYINT(1) DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    // player_seasons
    $pdo->exec("CREATE TABLE IF NOT EXISTS player_seasons (
        player_id INT NOT NULL,
        team_id INT NOT NULL,
        season_id INT NOT NULL,
        position VARCHAR(10) NULL,
        shirt_number INT NULL,
        on_loan TINYINT(1) DEFAULT 0,
        goals INT NOT NULL DEFAULT 0,
        assists INT NOT NULL DEFAULT 0,
        PRIMARY KEY(player_id, season_id),
        KEY idx_team (team_id),
        CONSTRAINT fk_ps_player FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
        CONSTRAINT fk_ps_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
        CONSTRAINT fk_ps_season FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Ajout rétro-actif des colonnes goals / assists si la table existait déjà
    try { $pdo->exec("ALTER TABLE player_seasons ADD COLUMN goals INT NOT NULL DEFAULT 0"); } catch (Throwable $e) {}
    try { $pdo->exec("ALTER TABLE player_seasons ADD COLUMN assists INT NOT NULL DEFAULT 0"); } catch (Throwable $e) {}
}

function getOrCreateSeason(PDO $pdo, string $name): int {
    $stmt = $pdo->prepare('SELECT id FROM seasons WHERE name = :n');
    $stmt->execute([':n' => $name]);
    $id = $stmt->fetchColumn();
    if ($id) return (int)$id;
    $ins = $pdo->prepare('INSERT INTO seasons(name, active) VALUES(:n, 1)');
    $ins->execute([':n' => $name]);
    return (int)$pdo->lastInsertId();
}

function getTeamId(PDO $pdo, string $teamName): ?int {
    $stmt = $pdo->prepare('SELECT id FROM teams WHERE nom = :n');
    $stmt->execute([':n' => $teamName]);
    $id = $stmt->fetchColumn();
    return $id ? (int)$id : null;
}

function getOrCreatePlayer(PDO $pdo, string $last, string $first, ?string $nat): int {
    $stmt = $pdo->prepare('SELECT id FROM players WHERE nom = :ln AND prenom = :fn LIMIT 1');
    $stmt->execute([':ln' => $last, ':fn' => $first]);
    $id = $stmt->fetchColumn();
    if ($id) return (int)$id;
    $ins = $pdo->prepare('INSERT INTO players(nom, prenom, nationalite) VALUES(:ln, :fn, :nat)');
    $ins->execute([':ln' => $last, ':fn' => $first, ':nat' => $nat]);
    return (int)$pdo->lastInsertId();
}

function linkPlayerSeason(PDO $pdo, int $playerId, int $teamId, int $seasonId, ?string $pos, ?int $num, int $loan, int $goals, int $assists) {
    $stmt = $pdo->prepare('REPLACE INTO player_seasons(player_id, team_id, season_id, position, shirt_number, on_loan, goals, assists) VALUES(:pid, :tid, :sid, :pos, :num, :loan, :goals, :assists)');
    $stmt->execute([
        ':pid' => $playerId,
        ':tid' => $teamId,
        ':sid' => $seasonId,
        ':pos' => $pos,
        ':num' => $num,
        ':loan' => $loan,
        ':goals' => $goals,
        ':assists' => $assists,
    ]);
}

$csvPath = __DIR__ . '/data/ligue1_2024_2025_players.csv';
if (!file_exists($csvPath)) {
    die('CSV introuvable: ' . $csvPath);
}

try {
    ensureSchema($pdo);
    $seasonId = getOrCreateSeason($pdo, '2024-2025');

    $handle = fopen($csvPath, 'r');
    if (!$handle) die('Impossible d\'ouvrir le CSV.');

    $header = fgetcsv($handle, 0, ',');
    $map = array_flip($header);
    $compte = 0; $ignores = 0; $erreurs = 0; $messages = [];

    while (($row = fgetcsv($handle, 0, ',')) !== false) {
        // Ignore lignes vides ou commentaires (# en première colonne)
        if (count(array_filter($row)) === 0) continue; // ligne vide
        if (isset($row[0]) && str_starts_with(trim($row[0]), '#')) continue; // commentaire
        $ln = trim($row[$map['player_last_name']] ?? '');
        $fn = trim($row[$map['player_first_name']] ?? '');
        $pos = trim($row[$map['position']] ?? '');
        $num = trim($row[$map['shirt_number']] ?? '');
        $teamName = trim($row[$map['team_name']] ?? '');
        $nat = trim($row[$map['nationality']] ?? '');
    $loan = (int)($row[$map['loan']] ?? 0);
    $goals = isset($map['goals']) && ctype_digit($row[$map['goals']]) ? (int)$row[$map['goals']] : 0;
    $assists = isset($map['assists']) && ctype_digit($row[$map['assists']]) ? (int)$row[$map['assists']] : 0;

        if ($ln === '' || $fn === '' || $teamName === '') {
            $ignores++; continue;
        }
        $teamId = getTeamId($pdo, $teamName);
        if (!$teamId) { $messages[] = "Equipe inconnue: $teamName (ligne ignorée)"; $ignores++; continue; }

        try {
            $playerId = getOrCreatePlayer($pdo, $ln, $fn, $nat ?: null);
            linkPlayerSeason($pdo, $playerId, $teamId, $seasonId, $pos ?: null, is_numeric($num)? (int)$num : null, $loan, $goals, $assists);
            $compte++;
        } catch (Throwable $e) {
            $erreurs++; $messages[] = 'Erreur joueur ' . $ln . ' ' . $fn . ': ' . $e->getMessage();
        }
    }
    fclose($handle);

    echo '<h2>Import terminé</h2>';
    echo '<p>Joueurs traités: ' . $compte . '</p>';
    echo '<p>Lignes ignorées: ' . $ignores . '</p>';
    echo '<p>Erreurs: ' . $erreurs . '</p>';
    if ($messages) {
        echo '<h3>Détails</h3><ul>'; foreach ($messages as $m) echo '<li>' . htmlspecialchars($m) . '</li>'; echo '</ul>';
    }
    echo '<p><a href="joueurs.php?season=2024-2025">Voir les joueurs</a></p>';
} catch (Throwable $e) {
    echo 'Erreur import: ' . htmlspecialchars($e->getMessage());
}
