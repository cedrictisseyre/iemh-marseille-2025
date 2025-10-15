<?php include __DIR__ . '/includes/flash.php'; $f = get_flash(); ?>
<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Accueil - F1 Database</title>
  <link rel='stylesheet' href='assets/style.css'>
</head>
<body>
<header>
  <img src="assets/logo-f1.svg" alt="Logo F1" style="height:48px;vertical-align:middle;margin-right:1em;">
  <h1 style="display:inline-block;vertical-align:middle;">Base de données Formule 1</h1>
</header>
<!-- Navigation removed from homepage; navigation is provided on all pages under /pages/ -->
<div class='container'>
  <?php if ($f): ?>
    <div class="flash flash-<?= htmlspecialchars($f['type']) ?>" style="padding:0.6em;border-radius:6px;margin-bottom:1em;background:#efe;color:#030;"><?= htmlspecialchars($f['message']) ?></div>
  <?php endif; ?>
  <h2>Navigation</h2>
  <ul>
    <li><a href='pages/liste_pilotes.php'>Liste des pilotes</a></li>
    <li><a href='pages/liste_ecuries.php'>Liste des écuries</a></li>
    <li><a href='pages/statistiques.php'>Statistiques</a></li>
    <li><a href='pages/recherche.php'>Recherche de pilotes</a></li>
    <li><a href='pages/comparer_pilotes.php'>Comparer deux pilotes</a></li>
    <li><a href='pages/palmares_annee.php'>Palmarès par année</a></li>
    <li><a href='pages/pantheon_pilotes.php'>Champions du monde</a></li>
    <li><a href='pages/ajout_participation.php'>Ajouter une participation</a></li>
  </ul>
  <p>Bienvenue sur le site de consultation des données F1 du projet IEMH Marseille 2025.</p>
  <h3>Formulaires d'ajout</h3>
  <ul>
    <li><a href="pages/ajout_pilote.php">Ajouter un pilote</a></li>
    <li><a href="pages/ajout_ecurie.php">Ajouter une écurie</a></li>
    <li><a href="pages/ajout_participation.php">Ajouter une participation</a></li>
  </ul>
  <div id="stats-globales" style="margin-top:2em;"></div>
  <script src="assets/stats.js"></script>
  <script src="assets/actions.js"></script>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
