<?php
require_once __DIR__ . '/../connexion.php';
$sql = 'SELECT * FROM equipes';
$stmt = $conn->query($sql);
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($equipes);
?>
