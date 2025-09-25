<?php
require_once '../connexion.php';
$sql = "SELECT * FROM joueurs";
$stmt = $conn->query($sql);
$joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($joueurs);
?>