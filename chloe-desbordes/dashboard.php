
<?php
require_once __DIR__ . '/connexion.php';
$nb_coureurs = $pdo->query('SELECT COUNT(*) FROM coureurs')->fetchColumn();
$nb_courses = $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$nb_resultats = $pdo->query('SELECT COUNT(*) FROM resultats')->fetchColumn();
$search = $_POST['search'] ?? '';
$result_search = [];
if ($search) {
    $stmt = $pdo->prepare('SELECT * FROM coureurs WHERE nom LIKE :search OR prenom LIKE :search');
    $stmt->execute(['search' => "%$search%"]);
    $result_search = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$top = $pdo->query('SELECT c.nom, c.prenom, MIN(r.temps) as meilleur_temps FROM resultats r JOIN coureurs c ON r.id_coureur = c.id_coureur GROUP BY r.id_coureur ORDER BY meilleur_temps ASC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
$labels = [];
$data = [];
foreach ($top as $row) {
    $labels[] = $row['prenom'] . ' ' . $row['nom'];
    $data[] = (float)$row['meilleur_temps'];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_all'])) {
    $data_export = [
        'coureurs' => $pdo->query('SELECT * FROM coureurs')->fetchAll(PDO::FETCH_ASSOC),
        'courses' => $pdo->query('SELECT * FROM courses')->fetchAll(PDO::FETCH_ASSOC),
        'resultats' => $pdo->query('SELECT * FROM resultats')->fetchAll(PDO::FETCH_ASSOC),
    ];
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="export_all.json"');
    echo json_encode($data_export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard UTMB</title>
    <style>
        body{font-family:Arial;max-width:1000px;margin:20px auto;padding:10px}
        section{border:1px solid #ddd;padding:12px;margin-bottom:12px;border-radius:6px}
        label{display:block;margin-top:6px}
        input[type=text], textarea, select{width:100%;padding:6px;margin-top:4px}
        button{padding:8px 12px;margin-top:8px}
        pre{background:#f7f7f7;padding:10px;border-radius:6px;overflow:auto}
        table{width:100%;border-collapse:collapse;margin-top:1em}
        th,td{border:1px solid #ddd;padding:6px;text-align:left}
        th{background:#f1f5f9}
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
    </section>
    <section>
        <h2>Rechercher un coureur</h2>
        <form method="post" style="margin-bottom:1em;">
            <input type="text" name="search" placeholder="Nom ou prénom..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Rechercher</button>
        </form>
        <?php if ($search): ?>
            <h3>Résultats pour "<?= htmlspecialchars($search) ?>" :</h3>
            <?php if (count($result_search) > 0): ?>
                <table>
                    <tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Sexe</th></tr>
                    <?php foreach ($result_search as $coureur): ?>
                        <tr>
                            <td><?= htmlspecialchars($coureur['id_coureur']) ?></td>
                            <td><?= htmlspecialchars($coureur['nom']) ?></td>
                            <td><?= htmlspecialchars($coureur['prenom']) ?></td>
                            <td><?= htmlspecialchars($coureur['sexe']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Aucun coureur trouvé.</p>
            <?php endif; ?>
        <?php endif; ?>
    </section>
    <section>
        <h2>Export des données</h2>
        <form method="post" style="display:inline-block;margin-right:1em;">
            <button type="submit" name="export_all">Exporter toutes les données (JSON)</button>
        </form>
        <button type="button" id="export-csv">Exporter toutes les données (CSV)</button>
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
    let csv = 'ID;Nom;Prénom;Sexe\n';
    <?php
    $all = $pdo->query('SELECT * FROM coureurs')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($all as $c) {
        echo "csv += '" . $c['id_coureur'] . ";" . addslashes($c['nom']) . ";" . addslashes($c['prenom']) . ";" . addslashes($c['sexe']) . "\\n';\n";
    }
    ?>
    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'coureurs.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>
</body>
</html>
