<?php
require_once __DIR__ . '/../connexion.php';
$sql = 'SELECT * FROM matchs';
$stmt = $conn->query($sql);
$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($matchs);
?>
