<?php
// Liste des événements à venir (championnats, stages...)
include_once __DIR__ . '/../../includes/db_connexion.php';


// Récupérer les actualités récentes (simulées si la table n’existe pas)
$actualites = [];
try {
    $actualites = $pdo->query('SELECT titre, contenu, date_pub FROM actualites ORDER BY date_pub DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Table actualites non présente, actualités simulées
    $actualites = [
        ['titre' => 'Stage régional', 'contenu' => 'Stage de perfectionnement le 15 octobre à Marseille.', 'date_pub' => date('Y-m-d')],
        ['titre' => 'Nouveau matériel', 'contenu' => 'Le club a reçu de nouveaux tatamis.', 'date_pub' => date('Y-m-d', strtotime('-2 days'))],
    ];
}

// Récupérer les événements à venir
$evenements = $pdo->query('SELECT nom, type, date_event, lieu FROM evenements WHERE date_event >= CURDATE() ORDER BY date_event ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Actualités</h2>
<ul>
    <?php foreach ($actualites as $a): ?>
    <li><strong><?= htmlspecialchars($a['titre']) ?></strong> <span style="color:gray;">(<?= htmlspecialchars($a['date_pub']) ?>)</span><br><?= htmlspecialchars($a['contenu']) ?></li>
    <?php endforeach; ?>
</ul>

<h2>Événements à venir</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>Nom</th>
        <th>Type</th>
        <th>Date</th>
        <th>Lieu</th>
    </tr>
    <?php foreach ($evenements as $e): ?>
    <tr>
        <td><?= htmlspecialchars($e['nom']) ?></td>
        <td><?= htmlspecialchars($e['type']) ?></td>
        <td><?= htmlspecialchars($e['date_event']) ?></td>
        <td><?= htmlspecialchars($e['lieu']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
