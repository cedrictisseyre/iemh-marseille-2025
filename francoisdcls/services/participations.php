<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
$sql = "SELECT * FROM participations ORDER BY annee DESC";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
