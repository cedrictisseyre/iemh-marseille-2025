<?php
require_once '../connexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO player_game_stats (game_id, player_id, team_id, starter, minutes, pts) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $_POST['game_id'],
        $_POST['player_id'],
        $_POST['team_id'],
        isset($_POST['starter']) ? 1 : 0,
        $_POST['minutes'] ?? null,
        $_POST['pts'] ?? 0
    ]);
    echo "Stats ajoutées !";
}
