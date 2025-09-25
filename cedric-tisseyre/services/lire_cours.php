<?php
require_once '../connexion.php';
$sql = "SELECT * FROM cours";
$stmt = $conn->query($sql);
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($cours);
?>