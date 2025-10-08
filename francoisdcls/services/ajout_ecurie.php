<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
// CSRF
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
	header('Location: ../pages/ajout_ecurie.php?error=csrf');
	exit;
}
$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
if ($nom === '') { header('Location: ../pages/ajout_ecurie.php?error=1'); exit; }
$sql = "INSERT INTO ecuries (nom) VALUES (?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nom]);
unset($_SESSION['csrf_token']);
header('Location: ../pages/liste_ecuries.php');
exit;
