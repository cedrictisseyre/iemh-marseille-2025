<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
try {
    $sql = "SELECT ecurie_id, nom_ecuries, pays FROM ecuries ORDER BY nom_ecuries";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $rows = $pdo->query("SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries")->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($rows);
