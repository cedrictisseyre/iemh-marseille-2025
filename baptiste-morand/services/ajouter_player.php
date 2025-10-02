<?php
require_once '../connexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO player (first_name, last_name, position, jersey_number, team_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['position'] ?? null,
        $_POST['jersey_number'] ?? null,
        $_POST['team_id']
    ]);
    echo "Joueur ajouté !";
}
