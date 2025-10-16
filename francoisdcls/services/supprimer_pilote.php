<?php

require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Méthode non autorisée';
    exit;
}

if (!validate_csrf()) {
    set_flash('error', 'Jeton CSRF invalide.');
    header('Location: ../pages/liste_pilotes.php');
    exit;
}

$id = isset($_POST['pilote_id']) ? (int)$_POST['pilote_id'] : 0;
if (!$id) {
    set_flash('error', 'ID pilote manquant');
    header('Location: ../pages/liste_pilotes.php');
    exit;
}

try {
    // Optionnel: supprimer participations liées
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('DELETE FROM participations WHERE pilote_id = ?');
    $stmt->execute([$id]);
    $stmt2 = $pdo->prepare('DELETE FROM championnats WHERE pilote_id = ?');
    $stmt2->execute([$id]);
    $stmt3 = $pdo->prepare('DELETE FROM pilotes WHERE pilote_id = ?');
    $stmt3->execute([$id]);
    $pdo->commit();
    set_flash('success', 'Pilote supprimé');
    header('Location: ../pages/liste_pilotes.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    header('Location: ../pages/liste_pilotes.php');
    exit;
}
