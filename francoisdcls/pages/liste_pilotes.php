<?php
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/photo_helper.php';
// récupérer photo et quelques statistiques pour chaque pilote
$sql = "SELECT p.*, 
  (SELECT COUNT(*) FROM championnats c WHERE c.pilote_id = p.pilote_id) AS nb_titres,
  (SELECT COUNT(*) FROM participations pa WHERE pa.pilote_id = p.pilote_id) AS nb_particip
  FROM pilotes p
  ORDER BY p.nom, p.prenom";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
// Charger la liste des écuries pour le formulaire de participation
$ecuries = $pdo->query("SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries")->fetchAll(PDO::FETCH_ASSOC);
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
  <div class="cards">
  <?php foreach($rows as $row):
    $img = resolve_photo_url($row['photo'] ?? null);
    $local = null; if ($img) $local = cached_image_url($img);
  ?>
    <article class="card">
      <a href="<?= '../pages/fiche_pilote.php?id=' . urlencode($row['pilote_id']) ?>" class="card-link">
        <?php if ($local): ?>
          <img src="<?= htmlspecialchars($local) ?>" alt="<?= htmlspecialchars($row['prenom'].' '.$row['nom']) ?>" width="72" height="72" loading="lazy">
        <?php elseif (!empty($row['photo'])): ?>
          <img src="<?= htmlspecialchars($row['photo']) ?>" alt="<?= htmlspecialchars($row['prenom'].' '.$row['nom']) ?>" width="72" height="72" loading="lazy">
        <?php else: ?>
          <div class="card photo-placeholder"><?= htmlspecialchars(substr(($row['prenom']??'')[0] . ($row['nom']??'')[0],0,2)) ?></div>
        <?php endif; ?>
        <div class="meta">
          <div class="name"><?= htmlspecialchars($row['prenom'].' '. $row['nom']) ?></div>
          <div class="sub">Titres: <?= (int)($row['nb_titres'] ?? 0) ?> • Part.: <?= (int)($row['nb_particip'] ?? 0) ?></div>
        </div>
      </a>
    </article>
  <?php endforeach; ?>
  </div>
</div>

<section class="add-form" style="margin-top:2em;border-top:1px solid #ddd;padding-top:1em;">
  <h2>Ajouter un pilote</h2>
  <form method='post' action='../services/ajout_pilote.php'>
    <label>Prénom:<br><input type='text' name='prenom' required></label><br>
    <label>Nom:<br><input type='text' name='nom' required></label><br>
    <label>Nationalité:<br><input type='text' name='nationalite'></label><br>
    <label>Photo URL:<br><input type='url' name='photo'></label><br>
    <hr>
    <h3>Ajouter aussi une participation (optionnel)</h3>
    <label>Écurie:<br>
      <select name='ecurie_id'>
        <option value=''>-- Aucune --</option>
        <?php foreach($ecuries as $e): ?>
          <option value='<?= $e['ecurie_id'] ?>'><?= htmlspecialchars($e['nom_ecuries']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br>
    <label>Année:<br><input type='number' name='annee' min='1900' max='2100'></label><br>
    <button type='submit'>Ajouter</button>
  </form>
  <a href="../site_f1.php">Retour à l'accueil</a>
</section>
</div>

<!-- participation moved to the add pages (ajout_pilote / ajout_ecurie) -->

<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
<?php
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/photo_helper.php';
// récupérer photo et quelques statistiques pour chaque pilote
$sql = "SELECT p.*, 
  (SELECT COUNT(*) FROM championnats c WHERE c.pilote_id = p.pilote_id) AS nb_titres,
  (SELECT COUNT(*) FROM participations pa WHERE pa.pilote_id = p.pilote_id) AS nb_particip
  FROM pilotes p
  ORDER BY p.nom, p.prenom";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
// Charger la liste des écuries pour le formulaire de participation
$ecuries = $pdo->query("SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries")->fetchAll(PDO::FETCH_ASSOC);
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

<?php
// use functions from includes/photo_helper.php
?>

<div class="pilot-list">
<?php foreach($rows as $row):
  $img = resolve_photo_url($row['photo'] ?? null);
  $local = null;
  if ($img) $local = cached_image_url($img);
  $initials = trim(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? ''));
  $initials = implode('', array_map(function($p){return strtoupper($p[0] ?? '');}, array_filter(explode(' ', $initials))));
?>
  <article class="pilot-fiche">
    <a class="pilot-link" href="<?= '../pages/fiche_pilote.php?id=' . urlencode($row['pilote_id']) ?>" aria-label="Fiche de <?= htmlspecialchars($row['prenom'].' '. $row['nom']) ?>">
      <div class="pilot-photo">
        <?php if ($local): ?>
  <img src="<?= htmlspecialchars($local) ?>" alt="Photo de <?= htmlspecialchars($row['prenom'].' '.$row['nom']) ?>" width="120" height="120" loading="lazy">
        <?php elseif ($img): ?>
  <img src="<?= htmlspecialchars($img) ?>" alt="Photo de <?= htmlspecialchars($row['prenom'].' '.$row['nom']) ?>" width="120" height="120" loading="lazy">
        <?php else: ?>
          <div class="placeholder"><?= htmlspecialchars($initials ?: '?') ?></div>
        <?php endif; ?>
      </div>
      <div class="pilot-meta">
        <div class="title"><?= htmlspecialchars($row['prenom'].' '. $row['nom']) ?></div>
        <div class="muted">Titres: <strong><?= (int)($row['nb_titres'] ?? 0) ?></strong> &nbsp; • &nbsp; Participations: <strong><?= (int)($row['nb_particip'] ?? 0) ?></strong></div>
        <?php if (!empty($row['nationalite']) || !empty($row['photo'])): ?>
          <div class="muted" style="margin-top:0.35rem"><?= !empty($row['nationalite']) ? htmlspecialchars($row['nationalite']) : '' ?></div>
        <?php endif; ?>
      </div>
    </a>
  </article>
<?php endforeach; ?>
</div>
</div>

<section class="add-form" style="margin-top:2em;border-top:1px solid #ddd;padding-top:1em;">
  <h2>Ajouter un pilote</h2>
  <form method='post' action='../services/ajout_pilote.php'>
    <label>Prénom:<br><input type='text' name='prenom' required></label><br>
    <label>Nom:<br><input type='text' name='nom' required></label><br>
    <label>Nationalité:<br><input type='text' name='nationalite'></label><br>
    <label>Photo URL:<br><input type='url' name='photo'></label><br>
    <hr>
    <h3>Ajouter aussi une participation (optionnel)</h3>
    <label>Écurie:<br>
      <select name='ecurie_id'>
        <option value=''>-- Aucune --</option>
        <?php foreach($ecuries as $e): ?>
          <option value='<?= $e['ecurie_id'] ?>'><?= htmlspecialchars($e['nom_ecuries']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br>
    <label>Année:<br><input type='number' name='annee' min='1900' max='2100'></label><br>
    <button type='submit'>Ajouter</button>
  </form>
  <a href="../site_f1.php">Retour à l'accueil</a>
</section>
</div>

<!-- participation moved to the add pages (ajout_pilote / ajout_ecurie) -->

<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
