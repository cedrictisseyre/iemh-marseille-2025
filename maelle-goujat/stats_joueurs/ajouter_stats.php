<?php
require_once __DIR__ . '/../connexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_match = $_POST['id_match'] ?? null;
    $id_joueur = $_POST['id_joueur'] ?? null;
    $essais = $_POST['essais'] ?? 0;
    $transformations = $_POST['transformations'] ?? 0;
    $penalites = $_POST['penalites'] ?? 0;
    $cartons_jaunes = $_POST['cartons_jaunes'] ?? 0;
    $cartons_rouges = $_POST['cartons_rouges'] ?? 0;
    $sql = 'INSERT INTO stats_joueurs (id_match, id_joueur, essais, transformations, penalites, cartons_jaunes, cartons_rouges) VALUES (?, ?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_match, $id_joueur, $essais, $transformations, $penalites, $cartons_jaunes, $cartons_rouges]);
    echo 'Statistiques ajoutées !';
}
?>
