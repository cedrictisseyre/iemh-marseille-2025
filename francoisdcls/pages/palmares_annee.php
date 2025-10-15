<?php
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../database/bdd_formule1.php';
$annee = isset($_GET['annee']) ? intval($_GET['annee']) : '';
$champion = null;
$participants = [];
if ($annee) {
    $sql = "SELECT p.nom, p.prenom, c.annee FROM championnats c JOIN pilotes p ON c.pilote_id = p.pilote_id WHERE c.annee = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$annee]);
    $champion = $stmt->fetch(PDO::FETCH_ASSOC);
    $sql2 = "SELECT p.nom, p.prenom FROM participations pa JOIN pilotes p ON pa.pilote_id = p.pilote_id WHERE pa.annee = ? ORDER BY p.nom, p.prenom";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute([$annee]);
    $participants = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
// Récupérer les années disponibles
$annees = $pdo->query("SELECT DISTINCT annee FROM championnats ORDER BY annee DESC")->fetchAll(PDO::FETCH_COLUMN);
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Palmarès par année</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<?php $page_title = 'Palmarès par année';
include __DIR__ . '/../includes/header.php'; ?>
<div class='container'>
<form method='get'>
  <label>Choisir une année :
    <select name='annee' required>
      <option value=''>-- Année --</option>
      <?php foreach ($annees as $a) : ?>
        <option value='<?= $a ?>' <?= $annee == $a ? 'selected' : '' ?>><?= $a ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button type='submit'>Voir</button>
</form>
<?php if ($annee) : ?>
  <h2>Champion <?= $annee ?></h2>
    <?php if ($champion) : ?>
    <p><b><?= htmlspecialchars($champion['prenom'] . ' ' . $champion['nom']) ?></b></p>
    <?php else : ?>
    <p>Aucun champion trouvé pour cette année.</p>
    <?php endif; ?>
  <h3>Pilotes participants</h3>
  <ul>
    <?php foreach ($participants as $p) : ?>
      <li><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
<a href='index.php'>Retour à l'accueil</a>
</div>
</div>
<a href="../site_f1.php">Retour à l'accueil</a>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
