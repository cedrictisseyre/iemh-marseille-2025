<?php
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
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
<table>
<tr><th>Photo</th><th>Nom</th><th>Prénom</th><th>Titres</th><th>Participations</th></tr>
<?php foreach($rows as $row): ?>
<tr>
  <td style="width:80px;">
    <?php if (!empty($row['photo'])): ?>
      <img src="<?= htmlspecialchars($row['photo']) ?>" alt="Photo de <?= htmlspecialchars($row['prenom'].' '.$row['nom']) ?>" style="width:64px;height:auto;border-radius:6px;">
    <?php else: ?>
      <div class="no-photo" style="width:64px;height:64px;border-radius:6px;background:#eee;display:flex;align-items:center;justify-content:center;color:#666;">?</div>
    <?php endif; ?>
  </td>
  <td><?= htmlspecialchars($row['nom']) ?></td>
  <td><?= htmlspecialchars($row['prenom']) ?></td>
  <td style="text-align:center"><?= (int)($row['nb_titres'] ?? 0) ?></td>
  <td style="text-align:center"><?= (int)($row['nb_particip'] ?? 0) ?></td>
</tr>
<?php endforeach; ?>
</table>
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
