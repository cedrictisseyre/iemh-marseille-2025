<?php
require_once '../connexion.php';
$stmt = $pdo->query('SELECT * FROM jours ORDER BY id');
$jours = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($jours);