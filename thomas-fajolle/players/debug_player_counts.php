<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connexion.php';

echo "<h2>Debug joueurs</h2>";

// Vérifie si schéma saisons actif
$useSeason = false;
try { $pdo->query("SELECT 1 FROM player_seasons LIMIT 1"); $useSeason = true; } catch (Throwable $e) { $useSeason = false; }

echo '<p>Schema saisons actif: '.($useSeason?'OUI':'NON').'</p>';

if ($useSeason) {
    $sql = "SELECT s.name AS saison, t.nom AS equipe, COUNT(*) nb
            FROM player_seasons ps
            JOIN seasons s ON ps.season_id = s.id
            JOIN teams t ON ps.team_id = t.id
            GROUP BY s.name, t.nom
            ORDER BY s.name, t.nom";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    echo '<table border="1" cellpadding="4"><tr><th>Saison</th><th>Équipe</th><th>Nombre joueurs</th></tr>';
    foreach ($rows as $r) {
        echo '<tr><td>'.htmlspecialchars($r['saison']).'</td><td>'.htmlspecialchars($r['equipe']).'</td><td>'.$r['nb'].'</td></tr>';    }
    echo '</table>';
    $total = $pdo->query("SELECT COUNT(*) FROM player_seasons")->fetchColumn();
    echo '<p>Total liaisons player_seasons: '.$total.'</p>';
} else {
    $sql = "SELECT t.nom AS equipe, COUNT(*) nb FROM players p JOIN teams t ON p.team_id = t.id GROUP BY t.nom ORDER BY t.nom";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    echo '<table border="1" cellpadding="4"><tr><th>Équipe</th><th>Nombre joueurs</th></tr>';
    foreach ($rows as $r) {
        echo '<tr><td>'.htmlspecialchars($r['equipe']).'</td><td>'.$r['nb'].'</td></tr>';    }
    echo '</table>';
    $total = $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn();
    echo '<p>Total players: '.$total.'</p>';
}

?>