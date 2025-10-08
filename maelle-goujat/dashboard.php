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
        <h1>Tableau de bord</h1>
        <section>
            <h2>Statistiques globales</h2>
            <ul>
                <li><strong>Nombre de joueurs :</strong> <?= $nb_joueurs ?></li>
                <li><strong>Nombre d'équipes :</strong> <?= $nb_equipes ?></li>
                <li><strong>Nombre de matchs :</strong> <?= $nb_matchs ?></li>
                <li><strong>Nombre de lignes de stats :</strong> <?= $nb_stats ?></li>
            </ul>
        </section>
        <section>
            <h2>Top 5 joueurs (essais marqués)</h2>
            <canvas id="chart-essais" width="400" height="200" style="background:#fff;border-radius:8px;"></canvas>
            <?php
            $top = $conn->query('SELECT j.nom, j.prenom, SUM(s.essais) as total_essais FROM stats_joueurs s JOIN joueurs j ON s.id_joueur = j.id_joueur GROUP BY s.id_joueur ORDER BY total_essais DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
            $labels = [];
            $data = [];
            foreach ($top as $row) {
                $labels[] = $row['prenom'] . ' ' . $row['nom'];
                $data[] = (int)$row['total_essais'];
            }
            ?>
        </section>
        <section>
            <h2>Export des données</h2>
            <form method="post" style="display:inline-block;margin-right:1em;">
                <button type="submit" name="export_all">Exporter toutes les données (JSON)</button>
            </form>
            <button type="button" id="export-csv">Exporter toutes les données (CSV)</button>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_all'])) {
                $data = [
                    'joueurs' => $conn->query('SELECT * FROM joueurs')->fetchAll(PDO::FETCH_ASSOC),
                    'equipes' => $conn->query('SELECT * FROM equipes')->fetchAll(PDO::FETCH_ASSOC),
                    'matchs' => $conn->query('SELECT * FROM matchs')->fetchAll(PDO::FETCH_ASSOC),
                    'stats_joueurs' => $conn->query('SELECT * FROM stats_joueurs')->fetchAll(PDO::FETCH_ASSOC),
                ];
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="export_all.json"');
                echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                exit;
            }
            ?>
        </section>
        <section>
            <a href="index.php">Retour à l'accueil</a>
        </section>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chart-essais').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>,
        datasets: [{
            label: 'Essais',
            data: <?= json_encode($data) ?>,
            backgroundColor: '#2563eb',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision:0 } } }
    }
});
// Export CSV (client)
document.getElementById('export-csv').onclick = function() {
    fetch('index.php')
        .then(r => r.text())
        .then(html => {
            // Simple extraction des tableaux HTML pour CSV
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            let csv = '';
            doc.querySelectorAll('table').forEach(table => {
                table.querySelectorAll('tr').forEach(row => {
                    let rowData = Array.from(row.children).map(td => '"' + td.textContent.replace(/"/g, '""') + '"').join(',');
                    csv += rowData + '\n';
                });
                csv += '\n';
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'export_all.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
};
</script>
</body>
</html>
