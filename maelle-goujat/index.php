<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/projet/connexion.php';
$joueurs = [];
try {
    $sql = 'SELECT * FROM joueurs';
    $stmt = $conn->query($sql);
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $erreur = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page d'accueil - Maëlle GOUJAT</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        h1 { color: #2c3e50; }
        .pub { background: #eaf7ff; border-left: 5px solid #3498db; padding: 15px; margin: 20px 0; font-size: 1.1em; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur la page de Maëlle GOUJAT</h1>
        <p>Cette page est générée en PHP pour le projet IEMH Marseille 2025.</p>
        <div class="pub">
            <strong>Publicité :</strong> <br>
            Découvrez nos solutions innovantes pour l'éducation et la réussite !<br>
            <a href="https://iemh-marseille.fr" target="_blank">En savoir plus</a>
        </div>
        <p>Accès rapide :</p>
        <ul>
            <li><a href="projet/joueurs/ajouter_joueur.php">Ajouter un joueur</a></li>
            <li><a href="projet/joueurs/lire_joueurs.php">Liste des joueurs (JSON)</a></li>
        </ul>
        <h2>Liste des joueurs</h2>
        <?php if (!empty($erreur)) : ?>
            <div style="color:red;">Erreur : <?= htmlspecialchars($erreur) ?></div>
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
    </div>
</body>
</html>