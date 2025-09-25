<?php
require_once __DIR__ . '/../connexion.php';
$sql = 'SELECT * FROM joueurs';
$stmt = $conn->query($sql);
$joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($joueurs);
?>
