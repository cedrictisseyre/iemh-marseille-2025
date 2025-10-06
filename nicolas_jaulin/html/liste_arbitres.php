<?php
require_once 'db_connect.php';
$stmt = $pdo->query('SELECT * FROM Arbitres');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des arbitres</title>
</head>
<body>
    <h1>Liste des arbitres</h1>
    <a href="ajout_arbitre.php">Ajouter un arbitre</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Nationalité</th>
        </tr>
        <?php while ($row = $stmt->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id_arbitre']) ?></td>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['prenom']) ?></td>
            <td><?= htmlspecialchars($row['nationalite']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>