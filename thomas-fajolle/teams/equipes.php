<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connexion.php';
require_once __DIR__ . '/../includes/header.php';

$sql = "SELECT * FROM teams ORDER BY nom";
$stmt = $pdo->query($sql);
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Liste des équipes</h2>
<table>
    <tr><th>Nom</th><th>Ville</th><th>Stade</th><th>Entraîneur</th></tr>
    <?php foreach ($equipes as $e): ?>
        <tr>
            <td><?= htmlspecialchars($e['nom']) ?></td>
            <td><?= htmlspecialchars($e['ville']) ?></td>
            <td><?= htmlspecialchars($e['stade']) ?></td>
            <td><?= htmlspecialchars($e['entraineur']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
