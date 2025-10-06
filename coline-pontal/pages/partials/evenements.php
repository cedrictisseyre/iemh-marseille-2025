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

// Faux événements ajoutés manuellement
$evenements = [
    [
        'nom' => 'Stage Ceinture Noire 3e Dan',
        'type' => 'Stage',
        'date_event' => '2025-10-04 au 2025-10-22',
        'lieu' => 'Paris'
    ],
    [
        'nom' => 'Championnat d’Europe',
        'type' => 'Championnat',
        'date_event' => '2025-12-08',
        'lieu' => 'Francfort'
    ],
    [
        'nom' => 'Stage Jeunes',
        'type' => 'Stage',
        'date_event' => '2025-11-15',
        'lieu' => 'Lyon'
    ]
];
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
