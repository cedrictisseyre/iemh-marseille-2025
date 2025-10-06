<?php
require_once '../connexion.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $stmt = $conn->prepare('SELECT nom, prenom, poste FROM joueurs WHERE id_joueur = ?');
    $stmt->execute([$id]);
    $joueur = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats = $conn->prepare('SELECT * FROM stats_match WHERE id_joueur = ?');
    $stats->execute([$id]);
    $stats = $stats->fetchAll(PDO::FETCH_ASSOC);
} else {
    $joueur = null;
    $stats = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques du joueur</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php if ($joueur): ?>
        <h1>Statistiques de <?php echo htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']); ?></h1>
        <p>Poste : <?php echo htmlspecialchars($joueur['poste']); ?></p>
        <table border="1">
            <tr>
                <th>Match</th><th>Points</th><th>Rebonds</th><th>Passes</th><th>Interceptions</th><th>Contres</th><th>Turnovers</th><th>Fautes</th>
            </tr>
            <?php foreach ($stats as $stat): ?>
                <tr>
                    <td><?php echo $stat['id_match']; ?></td>
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
        <p>Joueur non trouvé.</p>
    <?php endif; ?>
    <a href="liste.php">Retour à la liste des joueurs</a>
</body>
</html>
