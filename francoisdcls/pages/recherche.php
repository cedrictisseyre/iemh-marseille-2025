<?php
require_once __DIR__ . '/../database/bdd_formule1.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];
if ($q !== '') {
  $sql = "SELECT pilote_id, nom, prenom FROM pilotes WHERE nom LIKE ? OR prenom LIKE ? ORDER BY nom, prenom";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(["%$q%", "%$q%"]);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?><!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Recherche de pilotes F1</title>
  <link rel='stylesheet' href='../assets/style.css'>
</head>
<body>
<header><h1>Recherche de pilotes</h1></header>
<div class='container'>
<form method='get'>
  <input type='text' name='q' value='<?= htmlspecialchars($q) ?>' placeholder='Nom ou prénom...'>
  <button type='submit'>Rechercher</button>
</form>
<?php if($q !== ''): ?>
  <h2>Résultats pour "<?= htmlspecialchars($q) ?>"</h2>
  <?php if($results): ?>
    <ul>
    <?php foreach($results as $row): ?>
      <li><a href='fiche_pilote.php?id=<?= $row['pilote_id'] ?>'><?= htmlspecialchars($row['prenom'].' '.$row['nom']) ?></a></li>
    <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Aucun pilote trouvé.</p>
  <?php endif; ?>
<?php endif; ?>
<a href='liste_pilotes.php'>Voir tous les pilotes</a>
</div>
<footer>Projet IEMH Marseille 2025</footer>
</body>
</html>
