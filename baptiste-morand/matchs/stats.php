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
    <div style="display:flex;align-items:flex-start;">
        <img src="../assets/logo-montfort.png" alt="Logo Montfort Basket Club" style="height:100px;margin:20px 20px 0 20px;">
        <div>
            <h1 style="margin-top:30px; color:#2c3e50;">Statistiques NM3 du Montfort Basket Club</h1>
        </div>
    </div>
    <?php include '../menu.php'; ?>
    <h2 style="margin-left:30px; color:#2980b9;">Statistiques du match</h2>
    <?php if ($match): ?>
        <table border="1" style="margin-left:30px;">
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
        <p style="margin-left:30px;">Match non trouvé.</p>
    <?php endif; ?>
</body>
</html>
