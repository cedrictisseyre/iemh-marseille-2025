<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
$sql = "SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
