<?php
require_once __DIR__ . '/../connexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom_equipe'] ?? '';
    $ville = $_POST['ville'] ?? null;
    $pays = $_POST['pays'] ?? null;
    $sql = 'INSERT INTO equipes (nom_equipe, ville, pays) VALUES (?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nom, $ville, $pays]);
    echo 'Equipe ajoutée !';
}
?>
