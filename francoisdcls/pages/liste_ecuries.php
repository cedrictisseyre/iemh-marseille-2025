<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/flash.php';
$sql = "SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Liste des écuries F1</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Liste des écuries de F1'; include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
  <?php $f = get_flash(); if ($f): ?>
    <div class="flash flash-<?= htmlspecialchars($f['type']) ?>" style="padding:0.6em;border-radius:6px;margin-bottom:1em;background:#efe;color:#030;"><?= htmlspecialchars($f['message']) ?></div>
  <?php endif; ?>
<table>
<tr><th>Nom</th><th>Fiche</th></tr>
<?php foreach($rows as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nom_ecuries']) ?></td>
  <td><a href='fiche_ecurie.php?id=<?= $row['ecurie_id'] ?>'>Voir fiche</a></td>
</tr>
<?php endforeach; ?>
</table>
<a href="../site_f1.php">Retour à l'accueil</a> | <a href='liste_pilotes.php'>Voir les pilotes</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
