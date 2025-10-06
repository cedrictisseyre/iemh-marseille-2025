<?php
require_once base_path('includes/header.php');
require_once base_path('connexion.php');

// Récupération des joueurs avec leur équipe
$sql = "SELECT p.*, t.nom AS equipe 
        FROM players p 
        JOIN teams t ON p.team_id = t.id
        ORDER BY t.nom, p.nom";

$stmt = $pdo->query($sql);
$joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Joueurs</h2>
<table>
    <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Poste</th>
        <th>Numéro</th>
        <th>Équipe</th>
        <th>Nationalité</th>
    </tr>
    <?php foreach ($joueurs as $j): ?>
        <tr>
            <td><?= htmlspecialchars($j['nom']) ?></td>
            <td><?= htmlspecialchars($j['prenom']) ?></td>
            <td><?= htmlspecialchars($j['poste']) ?></td>
            <td><?= htmlspecialchars($j['numero']) ?></td>
            <td><?= htmlspecialchars($j['equipe']) ?></td>
            <td><?= htmlspecialchars($j['nationalite']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require_once base_path('includes/footer.php');
