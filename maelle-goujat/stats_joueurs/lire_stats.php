<?php
require_once __DIR__ . '/../connexion.php';
$sql = 'SELECT * FROM stats_joueurs';
$stmt = $conn->query($sql);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($stats);
?>
