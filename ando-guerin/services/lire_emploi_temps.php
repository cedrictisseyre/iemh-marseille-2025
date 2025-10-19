<?php
require_once '../connexion.php';
$where = '';
$params = [];
if (isset($_GET['week_start']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['week_start'])) {
	$where = ' WHERE week_start = :ws';
	$params[':ws'] = $_GET['week_start'];
}
$stmt = $conn->prepare('SELECT * FROM emploi_temps' . $where . ' ORDER BY jour_id, horaire_id');
$stmt->execute($params);
$emploi = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($emploi);