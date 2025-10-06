<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM pilotes WHERE pilote_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$pilote = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pilote) { echo "<p>Pilote introuvable.</p>"; exit; }
// Statistiques
$sql2 = "SELECT COUNT(*) FROM championnats WHERE pilote_id = ?";
$nb_titres = $pdo->prepare($sql2); $nb_titres->execute([$id]); $nb_titres = $nb_titres->fetchColumn();
$sql3 = "SELECT COUNT(DISTINCT annee) FROM participations WHERE pilote_id = ?";
$nb_particip = $pdo->prepare($sql3); $nb_particip->execute([$id]); $nb_particip = $nb_particip->fetchColumn();
$sql4 = "SELECT DISTINCT ecurie_id FROM participations WHERE pilote_id = ?";
$ecuries = $pdo->prepare($sql4); $ecuries->execute([$id]); $ecuries = $ecuries->fetchAll(PDO::FETCH_COLUMN);
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Fiche pilote - <?= htmlspecialchars($pilote['prenom'].' '.$pilote['nom']) ?></title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<header><h1><?= htmlspecialchars($pilote['prenom'].' '.$pilote['nom']) ?></h1></header>
<div class='container'>
<ul>
  <li><b>Nationalité :</b> <?= htmlspecialchars($pilote['nationalite'] ?? 'N/A') ?></li>
  <li><b>Nombre de titres :</b> <?= $nb_titres ?></li>
  <li><b>Nombre de participations :</b> <?= $nb_particip ?></li>
  <li><b>Écuries :</b> <?= implode(', ', $ecuries) ?: 'N/A' ?></li>
</ul>
<a href='liste_pilotes.php'>&larr; Retour à la liste</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
