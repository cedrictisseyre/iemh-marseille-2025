<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$siege = trim($_POST['siege'] ?? '');

if ($nom === '') {
    echo json_encode(['success' => false, 'message' => 'Nom requis']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO ecuries (nom, siege) VALUES (?, ?)');
    $stmt->execute([$nom, $siege]);
    $id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'message' => 'Écurie ajoutée', 'id' => (int)$id]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
