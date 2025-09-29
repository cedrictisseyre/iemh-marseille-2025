<?php
include '../db_connect.php';

$stmt = $pdo->query('SELECT * FROM courses');
$courses = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($courses);
