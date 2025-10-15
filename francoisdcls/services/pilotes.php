<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
$sql = "SELECT * FROM pilotes ORDER BY nom, prenom";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
