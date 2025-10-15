<?php

require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/insert_helpers.php';
require_once __DIR__ . '/../includes/photo_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Méthode non autorisée';
    exit;
}

$result = insert_pilote($pdo, $_POST);
if (!$result['success']) {
    set_flash('error', $result['message']);
    header('Location: ../pages/ajout_pilote.php');
    exit;
}
// If the form also provided participation data, try to add it
$pilote_id = (int)$result['id'];
// pré-télécharger l'image si fournie pour remplir le cache local
if (!empty($_POST['photo'])) {
    $photo_raw = trim((string)$_POST['photo']);
    $resolved = resolve_photo_url($photo_raw);
    if ($resolved) {
        // tente le cache (silencieux)
        @cached_image_url($resolved);
    }
}
$participation_msg = '';
if (!empty($_POST['ecurie_id']) && !empty($_POST['annee'])) {
    $ecurie_id = (int)$_POST['ecurie_id'];
    $annee = (int)$_POST['annee'];
    $p = insert_participation($pdo, $pilote_id, $ecurie_id, $annee);
    if (!$p['success']) {
        // Pilot added but participation failed
        set_flash('warning', $result['message'] . ' — participation non ajoutée: ' . $p['message']);
        header('Location: ../pages/fiche_pilote.php?id=' . $pilote_id);
        exit;
    } else {
        set_flash('success', $result['message'] . ' et participation ajoutée');
        header('Location: ../pages/fiche_pilote.php?id=' . $pilote_id);
        exit;
    }
}

// default: just pilot added
set_flash('success', $result['message']);
header('Location: ../pages/fiche_pilote.php?id=' . $pilote_id);
exit;
