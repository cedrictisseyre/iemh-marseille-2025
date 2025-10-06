<?php
require_once base_path('includes/header.php');
require_once base_path('connexion.php');

// Récupération des matchs avec équipes et compétitions
$sql = "SELECT m.*, 
               ht.nom AS home_team, 
               at.nom AS away_team, 
               c.nom AS competition 
        FROM matches m
        JOIN teams ht ON m.home_team_id = ht.id
        JOIN teams at ON m.away_team_id = at.id
        JOIN competitions c ON m.competition_id = c.id
        ORDER BY m.date_match DESC";

$stmt = $pdo->query($sql);
$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Résultats des matchs</h2>
<table>
    <tr>
        <th>Date</th>
        <th>Compétition</th>
        <th>Domicile</th>
        <th>Score</th>
        <th>Extérieur</th>
    </tr>
    <?php foreach ($matchs as $m): ?>
        <tr>
            <td><?= htmlspecialchars($m['date_match']) ?></td>
            <td><?= htmlspecialchars($m['competition']) ?></td>
            <td><?= htmlspecialchars($m['home_team']) ?></td>
            <td><?= htmlspecialchars($m['home_score']) . ' - ' . htmlspecialchars($m['away_score']) ?></td>
            <td><?= htmlspecialchars($m['away_team']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require_once base_path('includes/footer.php');
