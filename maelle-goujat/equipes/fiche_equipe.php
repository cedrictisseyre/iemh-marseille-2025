<?php
require_once __DIR__ . '/../connexion.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID équipe invalide.');
}
$id = (int)$_GET['id'];
$stmt = $conn->prepare('SELECT * FROM equipes WHERE id_equipe = ?');
$stmt->execute([$id]);
$equipe = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$equipe) die('Équipe introuvable.');
// Récupérer les joueurs de l'équipe
$stmt2 = $conn->prepare('SELECT * FROM joueurs WHERE id_equipe = ?');
$stmt2->execute([$id]);
$joueurs = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche équipe</title>
    <link rel="stylesheet" href="../style-accueil.css">
</head>
<body>
    <button id="toggle-dark" aria-label="Activer/désactiver le mode sombre" style="position:absolute;top:1em;right:1em;z-index:10;">🌙</button>
    <div class="container">
        <h1>Fiche équipe : <?= htmlspecialchars($equipe['nom_equipe']) ?></h1>
        <ul>
            <li><strong>ID :</strong> <?= htmlspecialchars($equipe['id_equipe']) ?></li>
            <li><strong>Ville :</strong> <?= htmlspecialchars($equipe['ville']) ?></li>
            <li><strong>Pays :</strong> <?= htmlspecialchars($equipe['pays']) ?></li>
        </ul>
        <h2>Joueurs de l'équipe</h2>
        <?php if (count($joueurs) > 0): ?>
        <ul>
            <?php foreach ($joueurs as $joueur): ?>
                <li><?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
            <p>Aucun joueur dans cette équipe.</p>
        <?php endif; ?>
        <a href="../index.php">Retour à l'accueil</a>
    </div>
<script>
// Mode sombre
const darkBtn = document.getElementById('toggle-dark');
darkBtn.onclick = function() {
    document.body.classList.toggle('dark');
    localStorage.setItem('darkmode', document.body.classList.contains('dark'));
};
if (localStorage.getItem('darkmode') === 'true') {
    document.body.classList.add('dark');
}
</script>
</body>
</html>
