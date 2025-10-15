<?php
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM ecuries WHERE ecurie_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$ecurie = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ecurie) {
    echo "<p>Écurie introuvable.</p>";
    exit;
}
// Pilotes ayant couru pour cette écurie
$sql2 = "SELECT DISTINCT p.pilote_id, p.nom, p.prenom FROM participations pa JOIN pilotes p ON pa.pilote_id = p.pilote_id WHERE pa.ecurie_id = ? ORDER BY p.nom, p.prenom";
$pilotes = $pdo->prepare($sql2);
$pilotes->execute([$id]);
$pilotes = $pilotes->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
    <?php $ecurie_title = htmlspecialchars($ecurie['nom_ecuries']); ?>
    <title>Fiche écurie - <?= $ecurie_title ?></title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = htmlspecialchars($ecurie['nom_ecuries']);
include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
<ul>
  <li>
    <b>Pays :</b>
    <?= htmlspecialchars($ecurie['pays'] ?? 'N/A') ?>
  </li>
</ul>
<h2>Pilotes ayant couru pour cette écurie</h2>
<ul>
<?php foreach ($pilotes as $p) : ?>
  <li><a href='fiche_pilote.php?id=<?= $p['pilote_id'] ?>'><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></a></li>
<?php endforeach; ?>
</ul>
<a href='liste_ecuries.php'>&larr; Retour à la liste</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
