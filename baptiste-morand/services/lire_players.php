<?php
require_once '../connexion.php';
$sql = "SELECT * FROM player";
$stmt = $conn->query($sql);
echo "<table border='1'><tr><th>ID</th><th>Prénom</th><th>Nom</th><th>Poste</th><th>Numéro</th><th>Équipe</th></tr>";
while ($row = $stmt->fetch()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['first_name']}</td><td>{$row['last_name']}</td><td>{$row['position']}</td><td>{$row['jersey_number']}</td><td>{$row['team_id']}</td></tr>";
}
echo "</table>";
