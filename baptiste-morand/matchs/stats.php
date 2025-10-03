<?php
require_once '../connexion.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $stmt = $conn->prepare('SELECT date_match, adversaire, lieu FROM matchs WHERE id_match = ?');
    $stmt->execute([$id]);
    $match = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats = $conn->prepare('SELECT s.*, j.nom, j.prenom FROM stats_match s JOIN joueurs j ON s.id_joueur = j.id_joueur WHERE s.id_match = ?');
    $stats->execute([$id]);
    $stats = $stats->fetchAll(PDO::FETCH_ASSOC);
} else {
    $match = null;
    $stats = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques du match</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php if ($match): ?>
        <h1>Statistiques du match du <?php echo htmlspecialchars($match['date_match']); ?> contre <?php echo htmlspecialchars($match['adversaire']); ?> (<?php echo htmlspecialchars($match['lieu']); ?>)</h1>
        <table border="1">
            <tr>
                <th>Joueur</th><th>Points</th><th>Rebonds</th><th>Passes</th><th>Interceptions</th><th>Contres</th><th>Turnovers</th><th>Fautes</th>
            </tr>
            <?php foreach ($stats as $stat): ?>
                <tr>
                    <td><?php echo htmlspecialchars($stat['prenom'] . ' ' . $stat['nom']); ?></td>
                    <td><?php echo $stat['pts']; ?></td>
                    <td><?php echo $stat['reb_tot']; ?></td>
                    <td><?php echo $stat['ast']; ?></td>
                    <td><?php echo $stat['stl']; ?></td>
                    <td><?php echo $stat['blk']; ?></td>
                    <td><?php echo $stat['turnovers']; ?></td>
                    <td><?php echo $stat['pf']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Match non trouvé.</p>
    <?php endif; ?>
    <a href="liste.php">Retour à la liste des matchs</a>
</body>
</html>
