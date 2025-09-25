<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/connexion.php';
$joueurs = $equipes = $matchs = $stats = [];
$erreur = '';
try {
    $sql = 'SELECT * FROM joueurs';
    $stmt = $conn->query($sql);
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sql = 'SELECT * FROM equipes';
    $stmt = $conn->query($sql);
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sql = 'SELECT * FROM matchs';
    $stmt = $conn->query($sql);
    $matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sql = 'SELECT * FROM stats_joueurs';
    $stmt = $conn->query($sql);
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $erreur = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page d'accueil - Maëlle GOUJAT</title>
    <link rel="stylesheet" href="style-accueil.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur la page de Maëlle GOUJAT</h1>
        <p>Cette page est générée en PHP pour le projet IEMH Marseille 2025.</p>
        <div class="pub">
            <strong>Publicité :</strong> <br>
            Découvrez le calendrier de la compétition élite 1 féminine pour la saison 2025-2026 !<br>
            <a href="https://competitions.ffr.fr/competitions/elite-1-feminine/calendrier.html" target="_blank">En savoir plus</a>
        </div>
        <p>Accès rapide :</p>
        <ul>
            <li><a href="joueurs/ajouter_joueur.php">Ajouter un joueur</a></li>
            <li><a href="joueurs/lire_joueurs.php">Liste des joueurs (JSON)</a></li>
        </ul>
        <h2>Liste des joueurs</h2>
        <?php if (!empty($erreur)) : ?>
            <div class="error">Erreur : <?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        <?php if (count($joueurs) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Poste</th>
                <th>ID équipe</th>
            </tr>
            <?php foreach ($joueurs as $joueur): ?>
            <tr>
                <td><?= htmlspecialchars($joueur['id_joueur']) ?></td>
                <td><?= htmlspecialchars($joueur['nom']) ?></td>
                <td><?= htmlspecialchars($joueur['prenom']) ?></td>
                <td><?= htmlspecialchars($joueur['poste']) ?></td>
                <td><?= htmlspecialchars($joueur['id_equipe']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Aucun joueur trouvé.</p>
        <?php endif; ?>

        <h2>Liste des équipes</h2>
        <?php if (count($equipes) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Ville</th>
                <th>Pays</th>
            </tr>
            <?php foreach ($equipes as $equipe): ?>
            <tr>
                <td><?= htmlspecialchars($equipe['id_equipe']) ?></td>
                <td><?= htmlspecialchars($equipe['nom_equipe']) ?></td>
                <td><?= htmlspecialchars($equipe['ville']) ?></td>
                <td><?= htmlspecialchars($equipe['pays']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Aucune équipe trouvée.</p>
        <?php endif; ?>

        <h2>Liste des matchs</h2>
        <?php if (count($matchs) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Lieu</th>
                <th>Équipe dom.</th>
                <th>Équipe ext.</th>
                <th>Score dom.</th>
                <th>Score ext.</th>
            </tr>
            <?php foreach ($matchs as $match): ?>
            <tr>
                <td><?= htmlspecialchars($match['id_match']) ?></td>
                <td><?= htmlspecialchars($match['date_match']) ?></td>
                <td><?= htmlspecialchars($match['lieu']) ?></td>
                <td><?= htmlspecialchars($match['equipe_dom']) ?></td>
                <td><?= htmlspecialchars($match['equipe_ext']) ?></td>
                <td><?= htmlspecialchars($match['score_dom']) ?></td>
                <td><?= htmlspecialchars($match['score_ext']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Aucun match trouvé.</p>
        <?php endif; ?>

        <h2>Statistiques des joueurs</h2>
        <?php if (count($stats) > 0): ?>
        <table>
            <tr>
                <th>ID match</th>
                <th>ID joueur</th>
                <th>Essais</th>
                <th>Transformations</th>
                <th>Pénalités</th>
                <th>Cartons jaunes</th>
                <th>Cartons rouges</th>
            </tr>
            <?php foreach ($stats as $stat): ?>
            <tr>
                <td><?= htmlspecialchars($stat['id_match']) ?></td>
                <td><?= htmlspecialchars($stat['id_joueur']) ?></td>
                <td><?= htmlspecialchars($stat['essais']) ?></td>
                <td><?= htmlspecialchars($stat['transformations']) ?></td>
                <td><?= htmlspecialchars($stat['penalites']) ?></td>
                <td><?= htmlspecialchars($stat['cartons_jaunes']) ?></td>
                <td><?= htmlspecialchars($stat['cartons_rouges']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Aucune statistique trouvée.</p>
        <?php endif; ?>
    </div>
</body>
</html>