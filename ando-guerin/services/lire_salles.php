<?php
require_once '../connexion.php';
$stmt = $conn->query('SELECT * FROM salles ORDER BY id');
$salles = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($salles);