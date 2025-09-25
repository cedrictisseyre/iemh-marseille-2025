<?php
require_once '../connexion.php';
$stmt = $pdo->query('SELECT * FROM emploi_temps ORDER BY jour_id, horaire_id');
$emploi = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($emploi);