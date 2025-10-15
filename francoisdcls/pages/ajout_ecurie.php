<?php require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
// charger pilotes pour option participation
$pilotes = $pdo->query("SELECT pilote_id, prenom, nom FROM pilotes ORDER BY nom, prenom")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Ajouter une écurie</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Ajouter une écurie';
include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
  <form method='post' action='../services/ajout_ecurie.php'>
    <label>Nom de l'écurie:<br><input type='text' name='nom' required></label><br>
    <label>Pays/Siege:<br><input type='text' name='pays'></label><br>
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
  <a href='../site_f1.php'>Retour à l'accueil</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
