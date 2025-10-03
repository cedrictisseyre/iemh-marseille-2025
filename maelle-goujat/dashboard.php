<?php
require_once __DIR__ . '/connexion.php';
// Statistiques globales
$nb_joueurs = $conn->query('SELECT COUNT(*) FROM joueurs')->fetchColumn();
$nb_equipes = $conn->query('SELECT COUNT(*) FROM equipes')->fetchColumn();
$nb_matchs = $conn->query('SELECT COUNT(*) FROM matchs')->fetchColumn();
$nb_stats = $conn->query('SELECT COUNT(*) FROM stats_joueurs')->fetchColumn();
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="style-accueil.css">
</head>
<body>
    <button id="toggle-dark" aria-label="Activer/désactiver le mode sombre" style="position:absolute;top:1em;right:1em;z-index:10;">🌙</button>
    <div class="container">
        <h1>Tableau de bord</h1>
        <ul>
            <li><strong>Nombre de joueurs :</strong> <?= $nb_joueurs ?></li>
            <li><strong>Nombre d'équipes :</strong> <?= $nb_equipes ?></li>
            <li><strong>Nombre de matchs :</strong> <?= $nb_matchs ?></li>
            <li><strong>Nombre de lignes de stats :</strong> <?= $nb_stats ?></li>
        </ul>
        <a href="index.php">Retour à l'accueil</a>
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
