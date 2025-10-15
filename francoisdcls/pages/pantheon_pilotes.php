<?php
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Panthéon des Pilotes Champions</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Panthéon des Pilotes Champions';
include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
  <div id="pantheon-pilotes"></div>
  <a href='../site_f1.php'>Retour à l'accueil</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
<script src="../assets/pantheon_pilotes.js"></script>
</body>
</html>
