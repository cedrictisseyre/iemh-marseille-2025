<?php
require_once '../connexion.php';
$sql = "SELECT p.`id-etudiant`, e.nom, p.`id_cour`, c.date FROM presence p JOIN étudiants e ON p.`id-etudiant` = e.id JOIN cours c ON p.`id_cour` = c.id";
$stmt = $conn->query($sql);
$presences = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($presences);
?>