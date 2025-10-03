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
    <button id="toggle-dark" aria-label="Activer/désactiver le mode sombre" style="position:absolute;top:1em;right:3.5em;z-index:10;">🌙</button>
    <button id="toggle-access" aria-label="Activer/désactiver le mode accessibilité forte" style="position:absolute;top:1em;right:1em;z-index:10;">🦾</button>
// Accessibilité forte (contraste élevé, police dyslexique)
const accessBtn = document.getElementById('toggle-access');
accessBtn.onclick = function() {
    document.body.classList.toggle('access-high');
    localStorage.setItem('access', document.body.classList.contains('access-high'));
};
if (localStorage.getItem('access') === 'true') {
    document.body.classList.add('access-high');
}
    <div class="container">
        <h1>Fiche joueur : <?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?>
            <span id="fav-star" title="Favori" style="font-size:1.2em;vertical-align:middle;cursor:pointer;">★</span>
        </h1>
<script>
// Affichage étoile favori sur fiche joueur
const star = document.getElementById('fav-star');
const favKey = 'joueur-<?= htmlspecialchars($joueur['id_joueur']) ?>';
function updateStar() {
    const favs = JSON.parse(localStorage.getItem('favs')||'{}');
    star.style.color = favs[favKey] ? '#facc15' : '#64748b';
    star.setAttribute('aria-pressed', !!favs[favKey]);
}
star.onclick = function() {
    let favs = JSON.parse(localStorage.getItem('favs')||'{}');
    if (favs[favKey]) { delete favs[favKey]; } else { favs[favKey]=true; }
    localStorage.setItem('favs', JSON.stringify(favs));
    updateStar();
};
updateStar();
</script>
        <ul>
            <li><strong>ID :</strong> <?= htmlspecialchars($joueur['id_joueur']) ?></li>
            <li><strong>Nom :</strong> <?= htmlspecialchars($joueur['nom']) ?></li>
            <li><strong>Prénom :</strong> <?= htmlspecialchars($joueur['prenom']) ?></li>
            <li><strong>Poste :</strong> <?= htmlspecialchars($joueur['poste']) ?></li>
            <li><strong>Équipe :</strong> <?= $equipe ? htmlspecialchars($equipe['nom_equipe']) : 'Aucune' ?></li>
        </ul>
    <button onclick="window.print()" style="margin-right:1em;">Imprimer / PDF</button>
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
