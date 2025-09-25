<?php
require_once '../connexion.php';
$stmt = $conn->query('SELECT * FROM horaires ORDER BY id');
$horaires = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($horaires);