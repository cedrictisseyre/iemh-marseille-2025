<?php
require_once '../connexion.php';
$sql = "SELECT * FROM game";
$stmt = $conn->query($sql);
echo "<table border='1'><tr><th>ID</th><th>Saison</th><th>Date/Heure</th><th>Journée</th><th>Lieu</th><th>Domicile</th><th>Extérieur</th><th>Score Domicile</th><th>Score Extérieur</th></tr>";
while ($row = $stmt->fetch()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['season']}</td><td>{$row['date_time']}</td><td>{$row['match_day']}</td><td>{$row['venue']}</td><td>{$row['home_team_id']}</td><td>{$row['away_team_id']}</td><td>{$row['home_score']}</td><td>{$row['away_score']}</td></tr>";
}
echo "</table>";
