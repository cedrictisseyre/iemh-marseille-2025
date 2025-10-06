<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
$sql = "SELECT ecurie_id, nom FROM ecuries ORDER BY nom";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Liste des écuries F1</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<header><h1>Liste des écuries de F1</h1></header>
<div class='container'>
<table>
<tr><th>Nom</th><th>Fiche</th></tr>
<?php foreach($rows as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nom']) ?></td>
  <td><a href='fiche_ecurie.php?id=<?= $row['ecurie_id'] ?>'>Voir fiche</a></td>
</tr>
<?php endforeach; ?>
</table>
<a href="../site_f1.php">Retour à l'accueil</a> | <a href='liste_pilotes.php'>Voir les pilotes</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
