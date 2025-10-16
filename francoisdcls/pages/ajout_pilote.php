<?php
// Formulaire d'ajout de pilote
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
// charger ecuries pour option participation
$ecuries = $pdo->query("SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Ajouter un pilote</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Ajouter un pilote';
include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
  <form method='post' action='../services/ajout_pilote.php'>
    <?= csrf_field() ?>
    <label>Prénom:<br><input type='text' name='prenom' required></label><br>
    <label>Nom:<br><input type='text' name='nom' required></label><br>
    <label>Nationalité:<br><input type='text' name='nationalite'></label><br>
    <label>Photo URL:<br><input type='url' name='photo'></label><br>
    <hr>
    <h3>Ajouter aussi une participation (optionnel)</h3>
    <label>Écurie:<br>
      <select name='ecurie_id'>
        <option value=''>-- Aucune --</option>
        <?php foreach ($ecuries as $e) : ?>
          <option value='<?= $e['ecurie_id'] ?>'><?= htmlspecialchars($e['nom_ecuries']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br>
    <label>Année:<br><input type='number' name='annee' min='1900' max='2100'></label><br>
    <button type='submit'>Ajouter</button>
  </form>
  <a href='../site_f1.php'>Retour à l'accueil</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
