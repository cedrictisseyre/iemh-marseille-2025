<?php
include '../db_connect.php';

$stmt = $pdo->query('SELECT DISTINCT nationalite FROM coureurs');
$nationalites = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($nationalites);
