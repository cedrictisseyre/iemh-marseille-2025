<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
$pdoLocal = get_pdo();
if (!$pdoLocal) {
	http_response_code(500);
	echo json_encode(['error' => 'Database unavailable']);
	exit;
}
$sql = "SELECT * FROM championnats ORDER BY annee DESC";
$rows = $pdoLocal->query($sql)->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
