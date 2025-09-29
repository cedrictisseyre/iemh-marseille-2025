<?php
include '../connexion.php';

$stmt = $pdo->query('SELECT * FROM points');
$points = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($points);
