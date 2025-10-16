<?php
include __DIR__ . '/includes/flash.php';
$f = get_flash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <?php
    $meta_desc = 'Base de données Formule 1 — pilotes, écuries, participations et statistiques ('
      . 'projet IEMH Marseille 2025).';
    ?>
  <meta name="description" content="<?= htmlspecialchars($meta_desc) ?>">
  <title>Base de données Formule 1</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<a class="skip-link" href="#main-content">Aller au contenu</a>

<header>
  <img
    src="assets/logo-f1.svg"
    alt="Logo F1"
    style="height:48px;vertical-align:middle;margin-right:1em;">
  <h1 style="display:inline-block;vertical-align:middle;">Base de données Formule 1</h1>
</header>

<nav aria-label="Navigation principale">
  <ul>
    <li><a href="pages/liste_pilotes.php">Liste des pilotes</a></li>
    <li><a href="pages/liste_ecuries.php">Liste des écuries</a></li>
    <li><a href="pages/statistiques.php">Statistiques</a></li>
    <li><a href="pages/recherche.php">Recherche de pilotes</a></li>
    <li><a href="pages/comparer_pilotes.php">Comparer deux pilotes</a></li>
    <li><a href="pages/palmares_annee.php">Palmarès par année</a></li>
    <li><a href="pages/pantheon_pilotes.php">Champions du monde</a></li>
    <li><a href="pages/ajout_participation.php">Ajouter une participation</a></li>
  </ul>
</nav>

<main id="main-content" role="main" class="container">
  <?php if ($f) : ?>
        <?php
        $flash_type = htmlspecialchars($f['type']);
        $flash_msg = htmlspecialchars($f['message']);
        ?>
    <div
      class="flash flash-<?= $flash_type ?>"
      style="padding:0.6em;border-radius:6px;margin-bottom:1em;background:#efe;color:#030;">
        <?= $flash_msg ?>
    </div>
  <?php endif; ?>

  <p>Bienvenue sur le site de consultation des données F1 du projet IEMH Marseille 2025.</p>

  <h3>Formulaires d'ajout</h3>
  <ul>
    <li><a href="pages/ajout_pilote.php">Ajouter un pilote</a></li>
    <li><a href="pages/ajout_ecurie.php">Ajouter une écurie</a></li>
    <li><a href="pages/ajout_participation.php">Ajouter une participation</a></li>
  </ul>

  <div id="stats-globales" style="margin-top:2em;"></div>
</main>

<script src="assets/stats.js" defer></script>
<script src="assets/actions.js" defer></script>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
