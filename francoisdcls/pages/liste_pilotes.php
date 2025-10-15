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
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
<?php $page_title = 'Liste des pilotes de F1'; include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>

<div class="pantheon-grid">
<?php foreach($rows as $row):
  $img = resolve_photo_url($row['photo'] ?? null);
  $local = null;
  if ($img) $local = cached_image_url($img);
  $annees = [];
  // Attempt to collect years of participations for display (optional)
  try {
    $stmt = $pdo->prepare("SELECT annee FROM participations WHERE pilote_id = ? ORDER BY annee ASC");
    $stmt->execute([$row['pilote_id']]);
    $annees = $stmt->fetchAll(PDO::FETCH_COLUMN);
  } catch (Exception $e) {
    $annees = [];
  }
?>
  <div class="pantheon-card">
    <div class="pantheon-photo">
      <?php if ($local): ?>
        <img src="<?= htmlspecialchars($local) ?>" alt="Photo de <?= htmlspecialchars($row['prenom'].' '.$row['nom']) ?>">
      <?php elseif ($img): ?>
        <img src="<?= htmlspecialchars($img) ?>" alt="Photo de <?= htmlspecialchars($row['prenom'].' '.$row['nom']) ?>">
      <?php else: ?>
        <div class='no-photo'>?</div>
      <?php endif; ?>
    </div>
    <div class="pantheon-info">
      <h3><?= htmlspecialchars($row['prenom']) ?> <span class="pantheon-nom"><?= htmlspecialchars($row['nom']) ?></span></h3>
      <div class="pantheon-titres"><span class="nb"><?= (int)($row['nb_titres'] ?? 0) ?></span> titre<?= ((int)($row['nb_titres'] ?? 0) > 1 ? 's' : '') ?></div>
      <div class="pantheon-annees"><span class="label">Années :</span> <?= $annees ? htmlspecialchars(implode(', ', $annees)) : 'N/A' ?></div>
  <div class="pantheon-annees"><span class="label">Nationalité :</span> <?= htmlspecialchars($row['nationalite'] ?? 'N/A') ?></div>
  <div class="pantheon-participations"><span class="label">Participations :</span> <?= (int)($row['nb_particip'] ?? 0) ?><?= $annees ? ' (' . htmlspecialchars(implode(', ', $annees)) . ')' : '' ?></div>
    </div>
    <div class="pantheon-actions" style="margin-top:0.6rem;display:flex;gap:0.4rem;justify-content:center">
      <a class="btn" href="<?= '../pages/edit_pilote.php?id=' . urlencode($row['pilote_id']) ?>" style="background:#fff;border:1px solid #ddd;padding:0.35rem 0.6rem;border-radius:6px">Éditer</a>
      <form method="post" action="../services/supprimer_pilote.php" onsubmit="return confirm('Supprimer le pilote <?= htmlspecialchars($row['prenom'].' '.$row['nom']) ?> ?')" style="display:inline">
        <input type="hidden" name="pilote_id" value="<?= (int)$row['pilote_id'] ?>">
        <button type="submit" style="background:#b00;color:#fff;padding:0.35rem 0.6rem;border:none;border-radius:6px">Supprimer</button>
      </form>
    </div>
  </div>
<?php endforeach; ?>
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
