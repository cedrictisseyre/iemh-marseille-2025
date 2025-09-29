<?php
include '../db_connect.php';

$stmt = $pdo->query('SELECT * FROM points');
$points = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($points);
