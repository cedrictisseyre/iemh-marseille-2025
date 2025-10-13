<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
header('Content-Type: application/json; charset=utf-8');

// Simple JSON API pour ajouter un pilote
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$prenom = trim($_POST['prenom'] ?? '');
$nom = trim($_POST['nom'] ?? '');
$nationalite = trim($_POST['nationalite'] ?? '');
$photo = trim($_POST['photo'] ?? '');

if ($prenom === '' || $nom === '') {
    echo json_encode(['success' => false, 'message' => 'Prénom et nom requis']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO pilotes (prenom, nom, nationalite, photo) VALUES (?, ?, ?, ?)');
    $stmt->execute([$prenom, $nom, $nationalite, $photo]);
    $id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'message' => 'Pilote ajouté', 'id' => (int)$id]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
