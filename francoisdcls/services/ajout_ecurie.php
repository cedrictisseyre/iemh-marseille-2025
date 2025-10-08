<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
if ($nom === '') { header('Location: ../pages/ajout_ecurie.php?error=1'); exit; }
$sql = "INSERT INTO ecuries (nom) VALUES (?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nom]);
header('Location: ../pages/liste_ecuries.php');
exit;
