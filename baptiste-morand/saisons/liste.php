<?php
require_once '../connexion.php';
// Récupère la liste des saisons
$stmt = $conn->query('SELECT id_saison, annee, description FROM saisons');
$saisons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des saisons</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h1>Liste des saisons</h1>
    <ul>
        <?php foreach ($saisons as $saison): ?>
            <li>
                <?php echo htmlspecialchars($saison['annee']); ?> : <?php echo htmlspecialchars($saison['description']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="../index.php">Retour à l'accueil</a>
</body>
</html>
