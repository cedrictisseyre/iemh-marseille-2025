<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') { echo json_encode([]); exit; }
$sql = "SELECT pilote_id, nom, prenom FROM pilotes WHERE nom LIKE ? OR prenom LIKE ? ORDER BY nom, prenom";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$q%", "%$q%"]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
