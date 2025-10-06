<?php
require_once '../connexion.php';
header('Content-Type: application/json');
$eleves = $conn->query('SELECT * FROM eleves ORDER BY nom, prenom')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($eleves);