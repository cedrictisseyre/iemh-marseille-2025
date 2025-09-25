<?php
include 'db_connect.php';
$stmt = $pdo->query('SELECT * FROM courses');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des courses</title>
</head>
<body>
    <h1>Liste des courses</h1>
    <a href="liste_coureurs.php">Coureurs</a> | <a href="liste_participations.php">Participations</a> | <a href="liste_points.php">Points ITRA</a>
    <table border="1">
        <tr>
            <th>ID</th><th>Nom</th><th>Distance (km)</th><th>Dénivelé (m)</th><th>Date</th>
        </tr>
        <?php while ($row = $stmt->fetch()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['id_course']) ?></td>
            <td><?= htmlspecialchars($row['nom_course']) ?></td>
            <td><?= htmlspecialchars($row['distance_km']) ?></td>
            <td><?= htmlspecialchars($row['denivele_m']) ?></td>
            <td><?= htmlspecialchars($row['date_course']) ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
