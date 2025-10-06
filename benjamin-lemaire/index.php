<?php
// Inclusion de la configuration
require_once 'config.php';

// Récupération des activités avec jointure sur utilisateurs et sports
$sql = "SELECT a.id, a.sport, a.date, a.temps, u.nom AS utilisateur, s.nom_sport
        FROM activites_sportives a
        LEFT JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur
        LEFT JOIN sports s ON a.id_sport = s.id_sport
        ORDER BY a.date DESC";
$stmt = $pdo->query($sql);
$activites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Strava Like</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Bienvenue sur mon application sportive !</h1>
    <div class="container">
        <p>
            <a href="add_activity.php" class="btn-ajout">Ajouter une activité</a> |
            <a href="users.php" class="btn-users">Gérer les utilisateurs</a>
        </p>
        <h2>Liste des activités</h2>
        <table class="table-activites">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Sport</th>
                    <th>Utilisateur</th>
                    <th>Temps (min)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activites as $act) : ?>
                <tr>
                    <td><?= htmlspecialchars($act['date']) ?></td>
                    <td><?= htmlspecialchars($act['nom_sport'] ?? $act['sport']) ?></td>
                    <td><?= htmlspecialchars($act['utilisateur'] ?? 'Inconnu') ?></td>
                    <td><?= htmlspecialchars($act['temps']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="js/app.js"></script>
</body>
</html>
