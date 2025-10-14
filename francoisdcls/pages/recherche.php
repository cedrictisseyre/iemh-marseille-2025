<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];
if ($q !== '') {
  $sql = "SELECT pilote_id, nom, prenom FROM pilotes WHERE nom LIKE ? OR prenom LIKE ? ORDER BY nom, prenom";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(["%$q%", "%$q%"]);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Recherche de pilotes F1</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Recherche de pilotes'; include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>

<form id="form-recherche" autocomplete="off">
  <input type="text" id="input-recherche" placeholder="Nom ou prénom...">
  <button type="submit">Rechercher</button>
</form>
<div id="resultats-recherche" style="margin-top:1em;"></div>
<a href="../site_f1.php">Retour à l'accueil</a> | <a href='liste_pilotes.php'>Voir tous les pilotes</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
<script src="../assets/recherche.js"></script>
</body>
</html>
