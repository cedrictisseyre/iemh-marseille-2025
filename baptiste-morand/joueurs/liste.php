<?php
include '../menu.php';
require_once '../connexion.php';
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
    <div style="display:flex;align-items:flex-start;">
        <img src="../assets/logo-montfort.png" alt="Logo Montfort Basket Club" style="height:100px;margin:20px 20px 0 20px;">
        <div>
            <h1 style="margin-top:30px; color:#2c3e50;">Statistiques NM3 du Montfort Basket Club</h1>
        </div>
    </div>
    <?php include '../menu.php'; ?>
    <h2 style="margin-left:30px; color:#2980b9;">Liste des joueurs</h2>
    <ul style="margin-left:30px;">
        <?php foreach ($joueurs as $joueur): ?>
            <li>
                <a href="stats.php?id=<?php echo $joueur['id_joueur']; ?>">
                    <?php echo htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']); ?> (<?php echo htmlspecialchars($joueur['poste']); ?>)
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
