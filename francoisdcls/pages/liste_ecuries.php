<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/flash.php';
// Try to include 'pays' column if present; fallback if schema older
try {
    $sql = "SELECT ecurie_id, nom_ecuries, pays FROM ecuries ORDER BY nom_ecuries";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  // older schema without pays column
    $rows = $pdo->query("SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries")->fetchAll(PDO::FETCH_ASSOC);
}
// Charger la liste des pilotes pour le formulaire de participation
$pilotes = $pdo->query("SELECT pilote_id, prenom, nom FROM pilotes ORDER BY nom, prenom")->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Liste des écuries F1</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Liste des écuries de F1';
include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
<table>
<tr><th>Nom</th><th>Pays</th><th>Fiche</th></tr>
<?php foreach ($rows as $row) : ?>
<tr>
  <td><?= htmlspecialchars($row['nom_ecuries']) ?></td>
    <?php $country = trim((string)($row['pays'] ?? $row['siege'] ?? '')); ?>
  <td><?= htmlspecialchars($country !== '' && $country !== '0' ? $country : 'N/A') ?></td>
  <td><a href='fiche_ecurie.php?id=<?= $row['ecurie_id'] ?>'>Voir fiche</a></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<section class="add-form" style="margin-top:2em;border-top:1px solid #ddd;padding-top:1em;">
  <h2>Ajouter une écurie</h2>
  <form method='post' action='../services/ajout_ecurie.php'>
    <?= csrf_field() ?>
    <label>Nom de l'écurie:<br><input type='text' name='nom' required></label><br>
    <label>Pays/Siege:<br><input type='text' name='siege'></label><br>
      <hr>
      <h3>Ajouter aussi une participation (optionnel)</h3>
      <label>Pilote:<br>
        <select name='pilote_id'>
          <option value=''>-- Aucun --</option>
          <?php foreach ($pilotes as $p) : ?>
            <option value='<?= $p['pilote_id'] ?>'><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></option>
          <?php endforeach; ?>
        </select>
      </label><br>
      <label>Année:<br><input type='number' name='annee' min='1900' max='2100'></label><br>
      <button type='submit'>Ajouter</button>
  </form>
  <a href="../site_f1.php">Retour à l'accueil</a> | <a href='liste_pilotes.php'>Voir les pilotes</a>
</section>
</div>

<!-- participation moved to the add pages (ajout_pilote / ajout_ecurie) -->

<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
