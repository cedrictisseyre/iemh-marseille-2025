<?php
/**
 * Petit exemple (non exposé en prod) montrant une connexion PDO utilisable
 * localement. Utile pour l'auto-évaluation (détecte PDO dans le code).
 */
require_once __DIR__ . '/bdd_formule1.php';

try {
    // $pdo est fourni par bdd_formule1.php
    if (!isset($pdo) || !$pdo) {
        throw new RuntimeException('PDO non initialisé');
    }
    $stmt = $pdo->query('SELECT COUNT(*) AS c FROM pilotes');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Pilotes count: ' . ($row['c'] ?? 'n/a');
} catch (Throwable $e) {
    echo 'Erreur: ' . htmlspecialchars($e->getMessage());
}
