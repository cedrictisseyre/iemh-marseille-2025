<?php

require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/photo_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Méthode non autorisée';
    exit;
}

$id = isset($_POST['pilote_id']) ? (int)$_POST['pilote_id'] : 0;
$prenom = trim((string)($_POST['prenom'] ?? ''));
$nom = trim((string)($_POST['nom'] ?? ''));
$nationalite = trim((string)($_POST['nationalite'] ?? ''));
$photo = trim((string)($_POST['photo'] ?? ''));

if (!$id || $prenom === '' || $nom === '') {
    set_flash('error', 'Données incomplètes pour la modification.');
    header('Location: ../pages/edit_pilote.php?id=' . $id);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE pilotes SET prenom = ?, nom = ?, nationalite = ?, photo = ? WHERE pilote_id = ?');
    $stmt->execute([$prenom, $nom, $nationalite, $photo, $id]);
    // attempt to cache remote photo if provided
    if ($photo) {
        $resolved = resolve_photo_url($photo);
        if ($resolved) {
            @cached_image_url($resolved);
        }
    }
    set_flash('success', 'Pilote mis à jour.');
    header('Location: ../pages/liste_pilotes.php');
    exit;
} catch (Exception $e) {
    set_flash('error', 'Erreur lors de la modification : ' . $e->getMessage());
    header('Location: ../pages/edit_pilote.php?id=' . $id);
    exit;
}
