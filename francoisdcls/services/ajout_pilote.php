<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/insert_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Méthode non autorisée'; exit; }

$result = insert_pilote($pdo, $_POST);
if (!$result['success']) {
    set_flash('error', $result['message']);
    header('Location: ../pages/ajout_pilote.php');
    exit;
}
set_flash('success', $result['message']);
header('Location: ../pages/fiche_pilote.php?id=' . $result['id']);
exit;
