<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
$pdoLocal = get_pdo();
if (!$pdoLocal) {
    http_response_code(500);
    echo json_encode(['error' => 'Database unavailable']);
    exit;
}
try {
    $sql = "SELECT ecurie_id, nom_ecuries, pays FROM ecuries ORDER BY nom_ecuries";
    $rows = $pdoLocal->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $rows = $pdoLocal->query("SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries")->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($rows);
