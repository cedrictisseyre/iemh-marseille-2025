<?php
require_once '../connexion.php';
$sql = "SELECT * FROM player_game_stats";
$stmt = $conn->query($sql);
echo "<table border='1'><tr><th>ID</th><th>Match</th><th>Joueur</th><th>Équipe</th><th>Titulaire</th><th>Minutes</th><th>Points</th></tr>";
while ($row = $stmt->fetch()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['game_id']}</td><td>{$row['player_id']}</td><td>{$row['team_id']}</td><td>{$row['starter']}</td><td>{$row['minutes']}</td><td>{$row['pts']}</td></tr>";
}
echo "</table>";
