<?php
require_once '../connexion.php';
// Récupère la liste des matchs
$stmt = $conn->query('SELECT id_match, date_match, adversaire, lieu FROM matchs');
$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des matchs</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h1>Liste des matchs</h1>
    <ul>
        <?php foreach ($matchs as $match): ?>
            <li>
                <a href="stats.php?id=<?php echo $match['id_match']; ?>">
                    <?php echo htmlspecialchars($match['date_match'] . ' vs ' . $match['adversaire'] . ' (' . $match['lieu'] . ')'); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="../index.php">Retour à l'accueil</a>
</body>
</html>
