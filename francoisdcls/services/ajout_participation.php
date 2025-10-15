<?php

require_once __DIR__ . '/../database/bdd_formule1.php';
session_start();
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/insert_helpers.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
$csrf_present = !empty($_POST['csrf_token']) && !empty($_SESSION['csrf_token']);
if ($csrf_present && !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    set_flash('error', 'Jeton CSRF invalide.');
    header('Location: ../site_f1.php');
    exit;
}
$pilote_id = isset($_POST['pilote_id']) ? intval($_POST['pilote_id']) : 0;
$ecurie_id = isset($_POST['ecurie_id']) ? intval($_POST['ecurie_id']) : 0;
$annee = isset($_POST['annee']) ? intval($_POST['annee']) : 0;
if (!$pilote_id || !$ecurie_id || !$annee) {
    set_flash('error', 'Tous les champs sont requis.');
    header('Location: ../site_f1.php');
    exit;
}

$result = insert_participation($pdo, $pilote_id, $ecurie_id, $annee);
if (!$result['success']) {
    set_flash('error', $result['message']);
    header('Location: ../site_f1.php');
    exit;
}
unset($_SESSION['csrf_token']);
set_flash('success', $result['message']);
header('Location: ../pages/palmares_annee.php?annee=' . urlencode($annee));
exit;
