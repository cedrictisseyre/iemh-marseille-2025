<?php
require_once __DIR__ . '/connexion.php';
// Statistiques globales
$nb_coureurs = $conn->query('SELECT COUNT(*) FROM coureurs')->fetchColumn();
$nb_courses = $conn->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$nb_resultats = $conn->query('SELECT COUNT(*) FROM resultats')->fetchColumn();
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord UTMB</title>
    <style>
        body{font-family:Arial;max-width:1000px;margin:20px auto;padding:10px}
        section{border:1px solid #ddd;padding:12px;margin-bottom:12px;border-radius:6px}
        label{display:block;margin-top:6px}
        input[type=text], textarea, select{width:100%;padding:6px;margin-top:4px}
        button{padding:8px 12px;margin-top:8px}
        pre{background:#f7f7f7;padding:10px;border-radius:6px;overflow:auto}
    </style>
</head>
<body>
    <h1>Tableau de bord UTMB</h1>
    <section>
        <h2>Statistiques globales</h2>
        <ul>
            <li><strong>Nombre de coureurs :</strong> <?= $nb_coureurs ?></li>
            <li><strong>Nombre de courses :</strong> <?= $nb_courses ?></li>
            <li><strong>Nombre de résultats :</strong> <?= $nb_resultats ?></li>
        </ul>
    </section>
    <section>
        <h2>Top 5 coureurs (meilleurs temps)</h2>
        <canvas id="chart-temps" width="400" height="200" style="background:#fff;border-radius:8px;"></canvas>
        <?php
        $top = $conn->query('SELECT c.nom, c.prenom, MIN(r.temps) as meilleur_temps FROM resultats r JOIN coureurs c ON r.id_coureur = c.id_coureur GROUP BY r.id_coureur ORDER BY meilleur_temps ASC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
        $labels = [];
        $data = [];
        foreach ($top as $row) {
            $labels[] = $row['prenom'] . ' ' . $row['nom'];
            $data[] = (float)$row['meilleur_temps'];
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
                'coureurs' => $conn->query('SELECT * FROM coureurs')->fetchAll(PDO::FETCH_ASSOC),
                'courses' => $conn->query('SELECT * FROM courses')->fetchAll(PDO::FETCH_ASSOC),
                'resultats' => $conn->query('SELECT * FROM resultats')->fetchAll(PDO::FETCH_ASSOC),
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chart-temps').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>,
        datasets: [{
            label: 'Meilleur temps',
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
+document.getElementById('export-csv').onclick = function() {
+    fetch('index.php')
+        .then(r => r.text())
+        .then(html => {
+            const parser = new DOMParser();
+            const doc = parser.parseFromString(html, 'text/html');
+            let csv = '';
+            doc.querySelectorAll('table').forEach(table => {
+                table.querySelectorAll('tr').forEach(row => {
+                    let rowData = Array.from(row.children).map(td => '"' + td.textContent.replace(/"/g, '""') + '"').join(',');
+                    csv += rowData + '\n';
+                });
+                csv += '\n';
+            });
+            const blob = new Blob([csv], { type: 'text/csv' });
+            const link = document.createElement('a');
+            link.href = URL.createObjectURL(blob);
+            link.download = 'export_all.csv';
+            document.body.appendChild(link);
+            link.click();
+            document.body.removeChild(link);
+        });
+};
</script>
</body>
</html>
