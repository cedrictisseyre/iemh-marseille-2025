<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
// Récupérer la liste des pilotes pour le formulaire
$pilotes = $pdo->query("SELECT pilote_id, nom, prenom FROM pilotes ORDER BY nom, prenom")->fetchAll(PDO::FETCH_ASSOC);
$id1 = isset($_GET['id1']) ? intval($_GET['id1']) : 0;
$id2 = isset($_GET['id2']) ? intval($_GET['id2']) : 0;
$stats1 = $stats2 = null;
if ($id1 && $id2 && $id1 != $id2) {
  $sql = "SELECT p.nom, p.prenom,
    (SELECT COUNT(*) FROM championnats WHERE pilote_id = p.pilote_id) as nb_titres,
    (SELECT COUNT(DISTINCT annee) FROM participations WHERE pilote_id = p.pilote_id) as nb_particip,
    (SELECT COUNT(DISTINCT ecurie_id) FROM participations WHERE pilote_id = p.pilote_id) as nb_ecuries
    FROM pilotes p WHERE p.pilote_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id1]);
  $stats1 = $stmt->fetch(PDO::FETCH_ASSOC);
  $stmt->execute([$id2]);
  $stats2 = $stmt->fetch(PDO::FETCH_ASSOC);
}
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Comparer deux pilotes F1</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Comparer deux pilotes'; include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
<form method='get'>
  <label>Pilote 1 :
    <select name='id1' required>
      <option value=''>-- Choisir --</option>
      <?php foreach($pilotes as $p): ?>
        <option value='<?= $p['pilote_id'] ?>' <?= $id1==$p['pilote_id']?'selected':'' ?>><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Pilote 2 :
    <select name='id2' required>
      <option value=''>-- Choisir --</option>
      <?php foreach($pilotes as $p): ?>
        <option value='<?= $p['pilote_id'] ?>' <?= $id2==$p['pilote_id']?'selected':'' ?>><?= htmlspecialchars($p['prenom'].' '.$p['nom']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button type='submit'>Comparer</button>
</form>
<?php if($stats1 && $stats2): ?>
  <h2>Comparaison</h2>
  <table>
    <tr><th></th><th><?= htmlspecialchars($stats1['prenom'].' '.$stats1['nom']) ?></th><th><?= htmlspecialchars($stats2['prenom'].' '.$stats2['nom']) ?></th></tr>
    <tr><td>Titres</td><td><?= $stats1['nb_titres'] ?></td><td><?= $stats2['nb_titres'] ?></td></tr>
    <tr><td>Participations</td><td><?= $stats1['nb_particip'] ?></td><td><?= $stats2['nb_particip'] ?></td></tr>
    <tr><td>Écuries</td><td><?= $stats1['nb_ecuries'] ?></td><td><?= $stats2['nb_ecuries'] ?></td></tr>
  </table>
<?php elseif($id1 && $id2 && $id1==$id2): ?>
  <p style='color:#b00'>Veuillez choisir deux pilotes différents.</p>
<?php endif; ?>
<a href='index.php'>Retour à l'accueil</a>
</div>
</div>
<a href="../site_f1.php">Retour à l'accueil</a>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
