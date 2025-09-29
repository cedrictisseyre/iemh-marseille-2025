<?php
include '../db_connect.php';

$stmt = $pdo->query('SELECT * FROM participations');
$participations = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($participations);
