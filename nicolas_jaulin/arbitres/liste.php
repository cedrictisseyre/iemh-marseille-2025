<?php
$php_debug = true;
if ($php_debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
require_once '../db_connect.php';
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
    <a href="ajout.php">Ajouter un arbitre</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Nationalité</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $stmt->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id_arbitre']) ?></td>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['prenom']) ?></td>
            <td><?= htmlspecialchars($row['nationalite']) ?></td>
            <td>
                <a href="modif.php?id=<?= $row['id_arbitre'] ?>">Modifier</a> |
                <a href="suppr.php?id=<?= $row['id_arbitre'] ?>" onclick="return confirm('Supprimer cet arbitre ?');">Supprimer</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </body>
    <p><a href="../index.php">Retour au menu principal</a></p>
</body>
</html>