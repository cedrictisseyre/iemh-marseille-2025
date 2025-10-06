<?php
include '../menu.php';
require_once '../connexion.php';
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
    <div style="display:flex;align-items:flex-start;">
        <img src="../assets/logo-montfort.png" alt="Logo Montfort Basket Club" style="height:100px;margin:20px 20px 0 20px;">
        <div>
            <h1 style="margin-top:30px; color:#2c3e50;">Statistiques NM3 du Montfort Basket Club</h1>
        </div>
    </div>
    <?php include '../menu.php'; ?>
    <h2 style="margin-left:30px; color:#2980b9;">Liste des saisons</h2>
    <ul style="margin-left:30px;">
        <?php foreach ($saisons as $saison): ?>
            <li>
                <?php echo htmlspecialchars($saison['annee']); ?> : <?php echo htmlspecialchars($saison['description']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
