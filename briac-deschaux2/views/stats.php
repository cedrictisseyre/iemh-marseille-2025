<?php
$stats = $pdo->query("SELECT s.*, p.nom, p.prenom, t.nom_team
                      FROM stats s
                      JOIN player p ON s.id_player = p.id_player
                      JOIN team t ON p.id_team = t.id_team
                      ORDER BY s.saison DESC")->fetchAll();
?>

<section class="section">
    <h2>Statistiques NFL</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Joueur</th>
                <th>Équipe</th>
                <th>Saison</th>
                <th>Yards (pass)</th>
                <th>TD (pass)</th>
                <th>Yards (rush)</th>
                <th>TD (rush)</th>
                <th>Réceptions</th>
                <th>Yards (receiving)</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($stats as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?></td>
                <td><?= htmlspecialchars($s['nom_team']) ?></td>
                <td><?= htmlspecialchars($s['saison']) ?></td>
                <td><?= htmlspecialchars($s['passing_yards']) ?></td>
                <td><?= htmlspecialchars($s['passing_tds']) ?></td>
                <td><?= htmlspecialchars($s['rushing_yards']) ?></td>
                <td><?= htmlspecialchars($s['rushing_tds']) ?></td>
                <td><?= htmlspecialchars($s['receptions']) ?></td>
                <td><?= htmlspecialchars($s['receiving_yards']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
