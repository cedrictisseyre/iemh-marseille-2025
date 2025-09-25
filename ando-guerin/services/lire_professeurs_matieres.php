<?php
require_once '../connexion.php';
$stmt = $pdo->query('SELECT * FROM professeurs_matieres ORDER BY professeur_id, matiere_id');
$data = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($data);