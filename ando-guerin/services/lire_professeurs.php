<?php
require_once '../connexion.php';
$stmt = $conn->query('SELECT * FROM professeurs ORDER BY id');
$profs = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($profs);