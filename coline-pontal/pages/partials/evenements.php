<?php
// Liste des événements à venir (championnats, stages...)
include_once __DIR__ . '/../../includes/db_connexion.php';

// Récupérer les événements à venir
$evenements = $pdo->query('SELECT nom, type, date_event, lieu FROM evenements WHERE date_event >= CURDATE() ORDER BY date_event ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
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
