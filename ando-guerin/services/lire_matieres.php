<?php
require_once '../connexion.php';
$stmt = $conn->query('SELECT * FROM matieres ORDER BY id');
$matieres = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($matieres);