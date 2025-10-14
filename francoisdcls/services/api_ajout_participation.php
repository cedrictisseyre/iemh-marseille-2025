<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/insert_helpers.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$pilote_id = intval($_POST['pilote_id'] ?? 0);
$ecurie_id = intval($_POST['ecurie_id'] ?? 0);
$annee = intval($_POST['annee'] ?? 0);

$result = insert_participation($pdo, $pilote_id, $ecurie_id, $annee);
if ($result['success']) {
    echo json_encode(['success'=>true,'message'=>$result['message'],'id'=>$result['id']]);
} else {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>$result['message']]);
}
