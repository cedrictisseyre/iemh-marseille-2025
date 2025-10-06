<?php
require_once 'db_connect.php';
$sql = 'SELECT ma.id_match, ma.id_arbitre, m.date_match, a.nom AS nom_arbitre, a.prenom AS prenom_arbitre FROM Matchs_Arbitres ma JOIN Matchs m ON ma.id_match = m.id_match JOIN Arbitres a ON ma.id_arbitre = a.id_arbitre';
$stmt = $pdo->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Matchs & Arbitres</title>
</head>
<body>
    <h1>Matchs & Arbitres</h1>
    <a href="ajout_match_arbitre.php">Assigner un arbitre à un match</a>
    <table border="1">
        <tr>
            <th>ID Match</th>
            <th>Date Match</th>
            <th>ID Arbitre</th>
            <th>Nom Arbitre</th>
            <th>Prénom Arbitre</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $stmt->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id_match']) ?></td>
            <td><?= htmlspecialchars($row['date_match']) ?></td>
            <td><?= htmlspecialchars($row['id_arbitre']) ?></td>
            <td><?= htmlspecialchars($row['nom_arbitre']) ?></td>
            <td><?= htmlspecialchars($row['prenom_arbitre']) ?></td>
            <td>
                <a href="supprimer_match_arbitre.php?id_match=<?= $row['id_match'] ?>&id_arbitre=<?= $row['id_arbitre'] ?>" onclick="return confirm('Supprimer cette association ?');">Supprimer</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>