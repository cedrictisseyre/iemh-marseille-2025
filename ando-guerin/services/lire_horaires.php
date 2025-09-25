<?php
require_once '../connexion.php';
$stmt = $pdo->query('SELECT * FROM horaires ORDER BY id');
$horaires = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($horaires);