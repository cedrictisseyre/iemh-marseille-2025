<?php
include 'db_connect.php';

// Affichage des nationalités des coureurs
$stmt = $pdo->query('SELECT DISTINCT nationalite FROM coureurs');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des nationalités</title>
</head>
<body>
    <h1>Liste des nationalités des coureurs</h1>
    <table border="1">
        <tr>
            <th>Nationalité</th>
        </tr>
        <?php while ($row = $stmt->fetch()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['nationalite']) ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
