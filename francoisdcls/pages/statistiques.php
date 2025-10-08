<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
// Top 5 pilotes avec le plus de titres
$sql = "SELECT p.nom, p.prenom, COUNT(c.championnat_id) as nb_titres FROM pilotes p JOIN championnats c ON p.pilote_id = c.pilote_id GROUP BY p.pilote_id ORDER BY nb_titres DESC, p.nom LIMIT 5";
$top_pilotes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
// Top 5 écuries avec le plus de participations
$sql2 = "SELECT e.nom, COUNT(pa.participation_id) as nb_particip FROM ecuries e JOIN participations pa ON e.ecurie_id = pa.ecurie_id GROUP BY e.ecurie_id ORDER BY nb_particip DESC, e.nom LIMIT 5";
$top_ecuries = $pdo->query($sql2)->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Statistiques F1</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<header><h1>Statistiques Formule 1</h1></header>
<div class='container'>
<h2>Top 5 pilotes les plus titrés</h2>
<table>
<tr><th>Nom</th><th>Prénom</th><th>Nombre de titres</th></tr>
<?php foreach($top_pilotes as $p): ?>
<tr><td><?= htmlspecialchars($p['nom']) ?></td><td><?= htmlspecialchars($p['prenom']) ?></td><td><?= $p['nb_titres'] ?></td></tr>
<?php endforeach; ?>
</table>
<h2>Top 5 écuries les plus présentes</h2>
<table>
<tr><th>Nom</th><th>Nombre de participations</th></tr>
<?php foreach($top_ecuries as $e): ?>
<tr><td><?= htmlspecialchars($e['nom']) ?></td><td><?= $e['nb_particip'] ?></td></tr>
<?php endforeach; ?>
</table>
<a href="../site_f1.php">Retour à l'accueil</a> | <a href='liste_pilotes.php'>Voir les pilotes</a> | <a href='liste_ecuries.php'>Voir les écuries</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
