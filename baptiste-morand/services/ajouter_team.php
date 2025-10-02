<?php
require_once '../connexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO team (name, category) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $_POST['name'],
        $_POST['category'] ?? null
    ]);
    echo "Équipe ajoutée !";
}
