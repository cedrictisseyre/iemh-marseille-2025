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
  <h3>Formulaires rapides</h3>
  <div class="quick-forms" style="display:flex;gap:1rem;flex-wrap:wrap;">
    <form id="quick-add-pilote" action="/francoisdcls/services/api_ajout_pilote.php" method="post" style="border:1px solid #ddd;padding:0.6em;border-radius:6px;width:280px;">
      <h4>Ajouter pilote</h4>
      <label>Prénom<br><input name="prenom" required></label><br>
      <label>Nom<br><input name="nom" required></label><br>
      <label>Nationalité<br><input name="nationalite"></label><br>
      <button type="submit">Ajouter</button>
    </form>

    <form id="quick-add-ecurie" action="/francoisdcls/services/api_ajout_ecurie.php" method="post" style="border:1px solid #ddd;padding:0.6em;border-radius:6px;width:280px;">
      <h4>Ajouter écurie</h4>
      <label>Nom<br><input name="nom" required></label><br>
      <label>Siège<br><input name="siege"></label><br>
      <button type="submit">Ajouter</button>
    </form>

    <form id="quick-add-participation" action="/francoisdcls/services/api_ajout_participation.php" method="post" style="border:1px solid #ddd;padding:0.6em;border-radius:6px;width:360px;">
      <h4>Ajouter participation</h4>
      <label>Pilote<br>
        <select name="pilote_id" required>
          <option value="">Chargement...</option>
        </select>
      </label><br>
      <label>Écurie<br>
        <select name="ecurie_id" required>
          <option value="">Chargement...</option>
        </select>
      </label><br>
      <label>Année<br><input name="annee" type="number" min="1900" max="2100" required></label><br>
      <button type="submit">Ajouter</button>
    </form>
  </div>
  <div id="stats-globales" style="margin-top:2em;"></div>
  <script src="assets/stats.js"></script>
  <script src="assets/actions.js"></script>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
