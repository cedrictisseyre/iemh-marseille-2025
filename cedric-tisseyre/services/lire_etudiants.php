<?php
require_once '../connexion.php';
$sql = "SELECT * FROM étudiants";
$stmt = $conn->query($sql);
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($etudiants);
?>