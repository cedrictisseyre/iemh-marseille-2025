<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
session_start();
// Validation simple
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Méthode non autorisée';
    exit;
}
// CSRF
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header('Location: ../pages/ajout_pilote.php?error=csrf');
    exit;
}
$prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$nationalite = isset($_POST['nationalite']) ? trim($_POST['nationalite']) : null;
$photo = isset($_POST['photo']) ? trim($_POST['photo']) : null;
if ($prenom === '' || $nom === '') {
    header('Location: ../pages/ajout_pilote.php?error=1');
    exit;
}
// Insert
$sql = "INSERT INTO pilotes (prenom, nom, nationalite, photo) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$prenom, $nom, $nationalite, $photo]);
$newId = $pdo->lastInsertId();
// Optionnel: régénérer le token
unset($_SESSION['csrf_token']);
// Redirect to fiche
header('Location: ../pages/fiche_pilote.php?id=' . $newId);
exit;
