<?php
require_once '../connexion.php';
// Récupère la liste des joueurs
$stmt = $conn->query('SELECT id_joueur, nom, prenom, poste FROM joueurs');
$joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des joueurs</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h1>Liste des joueurs</h1>
    <ul>
        <?php foreach ($joueurs as $joueur): ?>
            <li>
                <a href="stats.php?id=<?php echo $joueur['id_joueur']; ?>">
                    <?php echo htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']); ?> (<?php echo htmlspecialchars($joueur['poste']); ?>)
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="../index.php">Retour à l'accueil</a>
</body>
</html>
