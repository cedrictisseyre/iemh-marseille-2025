<?php
$classement = $pdo->query("
    SELECT t.nom_team, t.conference, COUNT(p.id_player) AS nb_joueurs
    FROM team t
    LEFT JOIN player p ON t.id_team = p.id_team
    GROUP BY t.id_team
    ORDER BY t.conference, nb_joueurs DESC
")->fetchAll();
?>

<section class="section">
    <h2>Classement des équipes</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Équipe</th>
                <th>Conférence</th>
                <th>Nb joueurs</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($classement as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['nom_team']) ?></td>
                <td><?= htmlspecialchars($c['conference']) ?></td>
                <td><?= htmlspecialchars($c['nb_joueurs']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
