<?php
require_once '../connexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO game (season, match_day, date_time, venue, home_team_id, away_team_id, home_score, away_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $_POST['season'],
        $_POST['match_day'] ?? null,
        $_POST['date_time'],
        $_POST['venue'] ?? null,
        $_POST['home_team_id'],
        $_POST['away_team_id'],
        $_POST['home_score'] ?? null,
        $_POST['away_score'] ?? null
    ]);
    echo "Match ajouté !";
}
