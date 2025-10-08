<?php
require_once __DIR__ . '/../includes/db_connect.php';

// Récupération des stats avec alias clairs
$sql = "SELECT 
    s.id_stat, s.id_player, s.saison,
    s.passing_yards, s.passing_tds, s.interceptions,
    s.rushing_yards, s.rushing_tds,
    s.receptions, s.receiving_yards, s.receiving_tds,
    s.tackles, s.sacks, s.interceptions_def,
    s.field_goals_made, s.field_goals_attempted,
    s.extra_points_made, s.extra_points_attempted,
    s.punts, s.punt_yards, s.longest_punt, s.inside_20,
    p.prenom, p.nom, p.poste,
    t.nom_team, t.logo_url
FROM stats s
JOIN player p ON s.id_player = p.id_player
JOIN team t ON p.id_team = t.id_team
ORDER BY p.nom";

$stmt = $pdo->query($sql);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Statistiques des joueurs</h2>

<?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
    <p style="color:green;">Statistiques ajoutées avec succès.</p>
<?php endif; ?>

<div class="stats-list">
    <?php foreach ($stats as $st): ?>
        <div class="stat-card">
            <h3>
                <img src="<?= htmlspecialchars($st['logo_url']) ?>" alt="Logo" style="height:20px;vertical-align:middle;">
                <?= htmlspecialchars($st['prenom'] . ' ' . $st['nom']) ?> (<?= htmlspecialchars($st['poste']) ?>) - Saison <?= htmlspecialchars($st['saison']) ?>
            </h3>

            <?php
            // On parcourt toutes les colonnes de stats
            foreach ($st as $key => $val) {
                if (in_array($key, [
                    'id_stat','id_player','prenom','nom','poste','saison','nom_team','logo_url'
                ], true)) {
                    continue; // on ignore les colonnes non stats
                }

                // Affichage spécial pour les Kickers (K) et Punters (P)
                if ($st['poste'] === 'K' && in_array($key, ['field_goals_made','extra_points_made'])) {
                    if ($key === 'field_goals_made') {
                        echo "<p><strong>Field Goals :</strong> {$st['field_goals_made']}/{$st['field_goals_attempted']}</p>";
                    }
                    if ($key === 'extra_points_made') {
                        echo "<p><strong>Extra Points :</strong> {$st['extra_points_made']}/{$st['extra_points_attempted']}</p>";
                    }
                    continue;
                }

                if ($st['poste'] === 'P' && $key === 'punts') {
                    echo "<p><strong>Punts :</strong> {$st['punts']} (Yards: {$st['punt_yards']}, Longest: {$st['longest_punt']}, Inside 20: {$st['inside_20']})</p>";
                    continue;
                }

                // Affichage générique pour toutes les autres stats
                if ($val !== null) {
                    $label = ucfirst(str_replace("_", " ", $key));
                    echo "<p><strong>" . htmlspecialchars($label) . ":</strong> " . htmlspecialchars((string)$val) . "</p>";
                }
            }
            ?>
        </div>
    <?php endforeach; ?>
</div>

