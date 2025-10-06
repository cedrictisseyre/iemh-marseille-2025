<?php
// Tableau de bord statistiques pour le club de karaté
include_once __DIR__ . '/../../includes/db_connexion.php';

// Nombre total d'adhérents
$total = $pdo->query('SELECT COUNT(*) FROM participants')->fetchColumn();

// Répartition par grade
$grades = $pdo->query('SELECT grade, COUNT(*) as nb FROM participants GROUP BY grade')->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Tableau de bord</h2>
<p>Nombre total d'adhérents : <strong><?= $total ?></strong></p>
<h3>Répartition par grade</h3>
<ul>
    <?php foreach ($grades as $g): ?>
        <li><?= htmlspecialchars($g['grade']) ?> : <?= $g['nb'] ?></li>
    <?php endforeach; ?>
</ul>
