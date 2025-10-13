<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$pilote_id = intval($_POST['pilote_id'] ?? 0);
$ecurie_id = intval($_POST['ecurie_id'] ?? 0);
$annee = intval($_POST['annee'] ?? 0);

if ($pilote_id <= 0 || $ecurie_id <= 0 || $annee <= 1880) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

try {
    // Vérifier doublon
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM participations WHERE pilote_id = ? AND ecurie_id = ? AND annee = ?');
    $stmt->execute([$pilote_id, $ecurie_id, $annee]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Participation déjà enregistrée']);
        exit;
    }
    $ins = $pdo->prepare('INSERT INTO participations (pilote_id, ecurie_id, annee) VALUES (?, ?, ?)');
    $ins->execute([$pilote_id, $ecurie_id, $annee]);
    echo json_encode(['success' => true, 'message' => 'Participation ajoutée']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
