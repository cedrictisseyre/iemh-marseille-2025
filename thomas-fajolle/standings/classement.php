<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connexion.php';
require_once __DIR__ . '/../includes/header.php';

$sql = "SELECT s.*, t.nom 
        FROM standings s
        JOIN teams t ON s.team_id = t.id
        ORDER BY s.points DESC, s.goal_difference DESC";
$stmt = $pdo->query($sql);
$classement = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Classement de la Ligue 1</h2>
<table>
    <tr><th>Équipe</th><th>Pts</th><th>J</th><th>G</th><th>N</th><th>P</th><th>BP</th><th>BC</th><th>Diff</th></tr>
    <?php foreach ($classement as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['nom']) ?></td>
            <td><?= $c['points'] ?></td>
            <td><?= $c['played'] ?></td>
            <td><?= $c['won'] ?></td>
            <td><?= $c['draw'] ?></td>
            <td><?= $c['lost'] ?></td>
            <td><?= $c['goals_for'] ?></td>
            <td><?= $c['goals_against'] ?></td>
            <td><?= $c['goal_difference'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
