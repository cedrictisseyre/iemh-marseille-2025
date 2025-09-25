<?php
require_once 'db_connect.php';
$stmt = $pdo->query('SELECT * FROM Joueurs');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des joueurs</title>
</head>
<body>
    <h1>Liste des joueurs</h1>
    <a href="ajout_joueur.php">Ajouter un joueur</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Date de naissance</th>
            <th>Poste</th>
            <th>ID Équipe</th>
        </tr>
        <?php while ($row = $stmt->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id_joueur']) ?></td>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['prenom']) ?></td>
            <td><?= htmlspecialchars($row['date_naissance']) ?></td>
            <td><?= htmlspecialchars($row['poste']) ?></td>
            <td><?= htmlspecialchars($row['id_equipe']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>