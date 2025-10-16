<?php

require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/insert_helpers.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// For AJAX/API clients, allow CSRF via header X-CSRF-Token matching session token
$csrf_header = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
if ($csrf_header) {
    if (!isset($_SESSION['_csrf_token']) || !hash_equals($_SESSION['_csrf_token'], $csrf_header)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Jeton CSRF invalide']);
        exit;
    }
}

$result = insert_pilote($pdo, $_POST);
if ($result['success']) {
    echo json_encode(['success' => true,'message' => $result['message'],'id' => $result['id']]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false,'message' => $result['message']]);
}
