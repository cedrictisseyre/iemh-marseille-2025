<?php
require_once 'db_connect.php';
$stmt = $pdo->query('SELECT * FROM Equipes');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des équipes</title>
</head>
<body>
    <h1>Liste des équipes</h1>
    <a href="ajout_equipe.php">Ajouter une équipe</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Ville</th>
            <th>Année de création</th>
        </tr>
        <?php while ($row = $stmt->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id_equipe']) ?></td>
            <td><?= htmlspecialchars($row['nom_equipe']) ?></td>
            <td><?= htmlspecialchars($row['ville']) ?></td>
            <td><?= htmlspecialchars($row['annee_creation']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>