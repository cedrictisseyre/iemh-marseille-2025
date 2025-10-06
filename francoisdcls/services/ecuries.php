<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
$sql = "SELECT * FROM ecuries ORDER BY nom";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
