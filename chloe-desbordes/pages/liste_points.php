<?php
include 'db_connect.php';
$sql = 'SELECT p.*, c.nom, c.prenom FROM points_ITRA p JOIN coureurs_UTMB c ON p.id_coureur = c.id_coureur';
$stmt = $pdo->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Points ITRA</title>
</head>
<body>
    <h1>Points ITRA</h1>
    <a href="liste_coureurs.php">Coureurs</a> | <a href="liste_courses.php">Courses</a> | <a href="liste_participations.php">Participations</a>
    <table border="1">
        <tr>
            <th>Coureur</th><th>Année</th><th>Points</th>
        </tr>
        <?php while ($row = $stmt->fetch()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['prenom']) ?> <?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['annee']) ?></td>
            <td><?= htmlspecialchars($row['points']) ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
