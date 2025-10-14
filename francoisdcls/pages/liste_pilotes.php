<?php
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
$sql = "SELECT pilote_id, nom, prenom FROM pilotes ORDER BY nom, prenom";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Liste des pilotes F1</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Liste des pilotes de F1'; include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
<table>
<tr><th>Nom</th><th>Prénom</th><th>Fiche</th></tr>
<?php foreach($rows as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nom']) ?></td>
  <td><?= htmlspecialchars($row['prenom']) ?></td>
  <td><a href='fiche_pilote.php?id=<?= $row['pilote_id'] ?>'>Voir fiche</a></td>
</tr>
<?php endforeach; ?>
</table>

  <a href="../site_f1.php">Retour à l'accueil</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
