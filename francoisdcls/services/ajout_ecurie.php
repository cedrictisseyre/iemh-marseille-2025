<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/insert_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$result = insert_ecurie($pdo, $_POST);
if (!$result['success']) {
	set_flash('error', $result['message']);
	header('Location: ../pages/ajout_ecurie.php');
	exit;
}
$ecurie_id = (int)$result['id'];
// If the form also provided participation data, try to add it
if (!empty($_POST['pilote_id']) && !empty($_POST['annee'])) {
	$pilote_id = (int)$_POST['pilote_id'];
	$annee = (int)$_POST['annee'];
	$p = insert_participation($pdo, $pilote_id, $ecurie_id, $annee);
	if (!$p['success']) {
		set_flash('warning', $result['message'] . ' — participation non ajoutée: ' . $p['message']);
		header('Location: ../pages/liste_ecuries.php');
		exit;
	} else {
		set_flash('success', $result['message'] . ' et participation ajoutée');
		header('Location: ../pages/fiche_ecurie.php?id=' . $ecurie_id);
		exit;
	}
}

set_flash('success', $result['message']);
header('Location: ../pages/liste_ecuries.php');
exit;
