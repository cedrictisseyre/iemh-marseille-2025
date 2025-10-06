<?php
require_once __DIR__ . '/../../config.php';
require_once base_path('connexion.php');
require_once base_path('includes/header.php');

// Récupération du classement avec les équipes
$sql = "SELECT s.*, t.nom 
        FROM standings s
        JOIN teams t ON s.team_id = t.id
        ORDER BY s.points DESC, s.goal_difference DESC";

$stmt = $pdo->query($sql);
$classement = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Classement de la Ligue 1</h2>
<table>
    <tr>
        <th>Équipe</th>
        <th>Pts</th>
        <th>J</th>
        <th>G</th>
        <th>N</th>
        <th>P</th>
        <th>BP</th>
        <th>BC</th>
        <th>Diff</th>
    </tr>
    <?php foreach ($classement as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['nom']) ?></td>
            <td><?= htmlspecialchars($c['points']) ?></td>
            <td><?= htmlspecialchars($c['played']) ?></td>
            <td><?= htmlspecialchars($c['won']) ?></td>
            <td><?= htmlspecialchars($c['draw']) ?></td>
            <td><?= htmlspecialchars($c['lost']) ?></td>
            <td><?= htmlspecialchars($c['goals_for']) ?></td>
            <td><?= htmlspecialchars($c['goals_against']) ?></td>
            <td><?= htmlspecialchars($c['goal_difference']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require_once base_path('includes/footer.php');
