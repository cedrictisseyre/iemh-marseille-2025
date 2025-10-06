<?php
// Tableau de bord statistiques pour le club de karaté
include_once __DIR__ . '/../../includes/db_connexion.php';


// Nombre total de karatekas
$nb_karatekas = $pdo->query('SELECT COUNT(*) FROM karateka')->fetchColumn();
// Nombre de clubs
$nb_clubs = $pdo->query('SELECT COUNT(*) FROM club')->fetchColumn();
// Statistiques de résultats par karateka
$stats = $pdo->query('SELECT k.nom, k.prenom, COUNT(p.id_participation) as participations, SUM(CASE WHEN p.resultat = "Victoire" THEN 1 ELSE 0 END) as victoires FROM karateka k LEFT JOIN participation p ON k.id_karateka = p.id_karateka GROUP BY k.id_karateka')->fetchAll(PDO::FETCH_ASSOC);

echo '<h2>Tableau de bord</h2>';
echo '<p>Nombre total de karatekas : <strong>' . $nb_karatekas . '</strong></p>';
echo '<p>Nombre de clubs : <strong>' . $nb_clubs . '</strong></p>';

echo '<h3>Résultats par karateka</h3>';
echo '<table border="1" cellpadding="5"><tr><th>Nom</th><th>Prénom</th><th>Participations</th><th>Victoires</th></tr>';
foreach ($stats as $s) {
    echo '<tr><td>' . htmlspecialchars($s['nom']) . '</td><td>' . htmlspecialchars($s['prenom']) . '</td><td>' . $s['participations'] . '</td><td>' . $s['victoires'] . '</td></tr>';
}
echo '</table>';


// Graphique à barres pour les médailles (données simulées)
$medailles = [
    'Or' => 5,
    'Argent' => 3,
    'Bronze' => 7
];
?>
<h3>Médailles remportées</h3>
<canvas id="medaillesChart" width="400" height="200"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('medaillesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Or', 'Argent', 'Bronze'],
        datasets: [{
            label: 'Nombre de médailles',
            data: [<?= $medailles['Or'] ?>, <?= $medailles['Argent'] ?>, <?= $medailles['Bronze'] ?>],
            backgroundColor: ['gold', 'silver', '#cd7f32']
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<?php
