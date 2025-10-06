<?php
require_once 'db_connect.php';
$stmt = $pdo->query('SELECT * FROM Matchs');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des matchs</title>
</head>
<body>
    <h1>Liste des matchs</h1>
    <a href="ajout_match.php">Ajouter un match</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Lieu</th>
            <th>Équipe Domicile</th>
            <th>Équipe Extérieure</th>
            <th>Score Dom</th>
            <th>Score Ext</th>
        </tr>
        <?php while ($row = $stmt->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id_match']) ?></td>
            <td><?= htmlspecialchars($row['date_match']) ?></td>
            <td><?= htmlspecialchars($row['lieu']) ?></td>
            <td><?= htmlspecialchars($row['id_equipe_dom']) ?></td>
            <td><?= htmlspecialchars($row['id_equipe_ext']) ?></td>
            <td><?= htmlspecialchars($row['score_dom']) ?></td>
            <td><?= htmlspecialchars($row['score_ext']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>