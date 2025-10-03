<?php
require_once __DIR__ . '/../connexion.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID joueur invalide.');
}
$id = (int)$_GET['id'];
$stmt = $conn->prepare('SELECT * FROM joueurs WHERE id_joueur = ?');
$stmt->execute([$id]);
$joueur = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$joueur) die('Joueur introuvable.');
// Récupérer le nom de l'équipe
$equipe = null;
if ($joueur['id_equipe']) {
    $stmt2 = $conn->prepare('SELECT * FROM equipes WHERE id_equipe = ?');
    $stmt2->execute([$joueur['id_equipe']]);
    $equipe = $stmt2->fetch(PDO::FETCH_ASSOC);
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche joueur</title>
    <link rel="stylesheet" href="../style-accueil.css">
</head>
<body>
    <div class="container">
        <h1>Fiche joueur : <?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?></h1>
        <ul>
            <li><strong>ID :</strong> <?= htmlspecialchars($joueur['id_joueur']) ?></li>
            <li><strong>Nom :</strong> <?= htmlspecialchars($joueur['nom']) ?></li>
            <li><strong>Prénom :</strong> <?= htmlspecialchars($joueur['prenom']) ?></li>
            <li><strong>Poste :</strong> <?= htmlspecialchars($joueur['poste']) ?></li>
            <li><strong>Équipe :</strong> <?= $equipe ? htmlspecialchars($equipe['nom_equipe']) : 'Aucune' ?></li>
        </ul>
        <a href="../index.php">Retour à l'accueil</a>
    </div>
</body>
</html>
