<?php
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
// Récupérer listes pilotes/ecuries
$pilotes = $pdo->query("SELECT pilote_id, prenom, nom FROM pilotes ORDER BY nom, prenom")->fetchAll(PDO::FETCH_ASSOC);
$ecuries = $pdo->query("SELECT ecurie_id, nom_ecuries FROM ecuries ORDER BY nom_ecuries")->fetchAll(PDO::FETCH_ASSOC);
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Ajouter une participation</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Ajouter une participation'; include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
  <form method='post' action='../services/ajout_participation.php'>
    <input type='hidden' name='csrf_token' value='<?= htmlspecialchars($_SESSION['csrf_token']) ?>'>
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
        <?php foreach($ecuries as $e): ?>
          <option value='<?= $e['ecurie_id'] ?>'><?= htmlspecialchars($e['nom_ecuries']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br>
    <label>Année:<br><input type='number' name='annee' min='1900' max='2100' required></label><br>
    <button type='submit'>Ajouter la participation</button>
  </form>
  <a href='../site_f1.php'>Retour à l'accueil</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
