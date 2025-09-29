<?php
include 'db_connect.php';
$stmt = $pdo->query('SELECT * FROM coureurs');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des coureurs UTMB</title>
</head>
<body>
    <h1>Liste des coureurs UTMB</h1>
    <a href="ajout_coureur.php">Ajouter un coureur</a> | <a href="liste_courses.php">Voir les courses</a> | <a href="liste_participations.php">Participations</a> | <a href="liste_points.php">Points ITRA</a>
    <table border="1">
        <tr>
            <th>ID</th><th>Nom</th><th>Prénom</th><th>Nationalité</th><th>Date de naissance</th><th>Club</th>
        </tr>
        <?php while ($row = $stmt->fetch()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['id_coureur']) ?></td>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['prenom']) ?></td>
            <td><?= htmlspecialchars($row['nationalite']) ?></td>
            <td><?= htmlspecialchars($row['date_naissance']) ?></td>
            <td><?= htmlspecialchars($row['club']) ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
