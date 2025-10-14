<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
require_once __DIR__ . '/../includes/flash.php';
$sql = "SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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
<?php $page_title = 'Liste des écuries de F1'; include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
<table>
<tr><th>Nom</th><th>Fiche</th></tr>
<?php foreach($rows as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nom_ecuries']) ?></td>
  <td><a href='fiche_ecurie.php?id=<?= $row['ecurie_id'] ?>'>Voir fiche</a></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<section class="add-form" style="margin-top:2em;border-top:1px solid #ddd;padding-top:1em;">
  <h2>Ajouter une écurie</h2>
  <form method='post' action='../services/ajout_ecurie.php'>
    <label>Nom de l'écurie:<br><input type='text' name='nom' required></label><br>
    <label>Pays/Siege:<br><input type='text' name='siege'></label><br>
    <button type='submit'>Ajouter</button>
  </form>
  <a href="../site_f1.php">Retour à l'accueil</a> | <a href='liste_pilotes.php'>Voir les pilotes</a>
</section>
</div>

<section class="add-participation" style="margin-top:2em;border-top:1px solid #ddd;padding-top:1em;">
  <h2>Ajouter une participation</h2>
  <form method='post' action='../services/ajout_participation.php'>
    <label>Pilote:<br>
      <select name='pilote_id' required>
        <option value=''>-- Choisir --</option>
        <?php foreach($pilotes as $p): ?>
          <option value='<?= $p['pilote_id'] ?>'><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br>
    <label>Écurie:<br>
      <select name='ecurie_id' required>
        <option value=''>-- Choisir --</option>
        <?php foreach($rows as $e): ?>
          <option value='<?= $e['ecurie_id'] ?>'><?= htmlspecialchars($e['nom_ecuries']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br>
    <label>Année:<br><input type='number' name='annee' min='1900' max='2100' required></label><br>
    <button type='submit'>Ajouter la participation</button>
  </form>
</section>

<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
