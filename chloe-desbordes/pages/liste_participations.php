<?php
include 'db_connect.php';
$sql = 'SELECT p.*, c.nom, c.prenom, cs.nom_course FROM participation p JOIN coureurs_UTMB c ON p.id_coureur = c.id_coureur JOIN courses cs ON p.id_course = cs.id_course';
$stmt = $pdo->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des participations</title>
</head>
<body>
    <h1>Liste des participations</h1>
    <a href="liste_coureurs.php">Coureurs</a> | <a href="liste_courses.php">Courses</a> | <a href="liste_points.php">Points ITRA</a>
    <table border="1">
        <tr>
            <th>ID</th><th>Coureur</th><th>Course</th><th>Dossard</th><th>Temps final</th><th>Classement</th><th>Statut</th>
        </tr>
        <?php while ($row = $stmt->fetch()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['id_participation']) ?></td>
            <td><?= htmlspecialchars($row['prenom']) ?> <?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['nom_course']) ?></td>
            <td><?= htmlspecialchars($row['dossard']) ?></td>
            <td><?= htmlspecialchars($row['temps_final']) ?></td>
            <td><?= htmlspecialchars($row['classement']) ?></td>
            <td><?= htmlspecialchars($row['statut']) ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
