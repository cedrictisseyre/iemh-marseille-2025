<?php
require_once __DIR__ . '/../config/database_connexion.php';

header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
$q = trim($q);
if ($q === '') { echo json_encode([]); exit; }

$stmt = $pdo->prepare("SELECT id_player, prenom, nom FROM player 
                       WHERE prenom LIKE ? OR nom LIKE ? OR CONCAT(prenom,' ',nom) LIKE ? OR CONCAT(nom,' ',prenom) LIKE ?
                       ORDER BY nom LIMIT 10");
$like = "%$q%";
$stmt->execute([$like,$like,$like,$like]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
