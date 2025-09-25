<?php
require_once 'db_connect.php';
$sql = 'SELECT sj.id_stat, sj.id_match, sj.id_joueur, j.nom, j.prenom, sj.essais, sj.plaquages, sj.passes_decisives, sj.cartons_jaunes FROM Statistiques_Joueur sj JOIN Joueurs j ON sj.id_joueur = j.id_joueur';
$stmt = $pdo->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques Joueurs</title>
</head>
<body>
    <h1>Statistiques des joueurs par match</h1>
    <a href="ajout_stat_joueur.php">Ajouter une statistique</a>
    <table border="1">
        <tr>
            <th>ID Stat</th>
            <th>ID Match</th>
            <th>ID Joueur</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Essais</th>
            <th>Plaquages</th>
            <th>Passes décisives</th>
            <th>Cartons jaunes</th>
        </tr>
        <?php while ($row = $stmt->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id_stat']) ?></td>
            <td><?= htmlspecialchars($row['id_match']) ?></td>
            <td><?= htmlspecialchars($row['id_joueur']) ?></td>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['prenom']) ?></td>
            <td><?= htmlspecialchars($row['essais']) ?></td>
            <td><?= htmlspecialchars($row['plaquages']) ?></td>
            <td><?= htmlspecialchars($row['passes_decisives']) ?></td>
            <td><?= htmlspecialchars($row['cartons_jaunes']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>