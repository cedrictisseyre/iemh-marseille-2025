<?php
include '../connexion.php';
header('Content-Type: application/json');

$stmt = $pdo->query('SELECT * FROM coureurs');
$coureurs = $stmt->fetchAll();
echo json_encode($coureurs);
