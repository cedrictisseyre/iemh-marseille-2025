<?php
require_once 'connexion.php';
include 'header.html';

// --- Gestion Ajout d'une mesure ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $id_client = $_POST['id_client'];
    $date_mesure = $_POST['date_mesure'];
    $masse = $_POST['masse'];

    if ($id_client && $date_mesure && $masse) {
        $stmt = $conn->prepare("INSERT INTO suivi_masse (id_client, date_mesure, masse) VALUES (:id_client, :date_mesure, :masse)");
        $stmt->execute([':id_client' => $id_client, ':date_mesure' => $date_mesure, ':masse' => $masse]);
        echo "<div class='alert alert-success text-center'>✅ Mesure ajoutée avec succès.</div>";
    } else {
        echo "<div class='alert alert-warning text-center'>⚠️ Tous les champs doivent être remplis.</div>";
    }
}

// --- Gestion Suppression ---
if (isset($_GET['supprimer'])) {
    $id_mesure = intval($_GET['supprimer']);
    $stmt = $conn->prepare("DELETE FROM suivi_masse WHERE id = :id");
    $stmt->execute([':id' => $id_mesure]);
    echo "<div class='alert alert-danger text-center'>🗑 Mesure supprimée.</div>";
}

// --- Récupération de tous les clients ---
$clients = $conn->query("SELECT id, prenom, nom FROM clients ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="text-center my-4">
    <h1>⚖️ Suivi de la masse corporelle</h1>
    <p class="text-muted">Visualisez et suivez l’évolution du poids de chaque client.</p>
    <a href="index.php" class="btn btn-secondary mt-3">🏠 Retour à l'accueil</a>
</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container">
<?php
$colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6610f2', '#6f42c1', '#fd7e14'];
$colorIndex = 0;

foreach ($clients as $client) {
    echo "<div class='card shadow-sm mb-5'>";
    echo "<div class='card-header bg-dark text-white'>
            <strong>{$client['prenom']} {$client['nom']}</strong>
          </div>";
    echo "<div class='card-body'>";

    // --- Récupération des mesures ---
    $stmt = $conn->prepare("SELECT id, date_mesure, masse FROM suivi_masse WHERE id_client = :id_client ORDER BY date_mesure ASC");
    $stmt->execute([':id_client' => $client['id']]);
    $mesures = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si pas de mesures, insérer quelques exemples
    if (count($mesures) === 0) {
        $fake = [
            ['date_mesure' => '2025-01-01', 'masse' => rand(70, 80)],
            ['date_mesure' => '2025-02-01', 'masse' => rand(70, 80)],
            ['date_mesure' => '2025-03-01', 'masse' => rand(70, 80)],
            ['date_mesure' => '2025-04-01', 'masse' => rand(70, 80)]
        ];
        $mesures = $fake;
    }

    // --- Formulaire d’ajout de mesure ---
    echo "
    <form method='POST' class='row g-3 mb-4'>
        <input type='hidden' name='ajouter' value='1'>
        <input type='hidden' name='id_client' value='{$client['id']}'>
        <div class='col-md-5'>
            <label>Date :</label>
            <input type='date' name='date_mesure' class='form-control' required>
        </div>
        <div class='col-md-5'>
            <label>Masse (kg) :</label>
            <input type='number' step='0.1' name='masse' class='form-control' required>
        </div>
        <div class='col-md-2 d-flex align-items-end'>
            <button type='submit' class='btn btn-success w-100'>Ajouter</button>
        </div>
    </form>
    ";

    // --- Tableau des mesures ---
    echo "<table class='table table-striped text-center'>
            <thead class='table-light'>
                <tr><th>Date</th><th>Masse (kg)</th><th>Actions</th></tr>
            </thead>
            <tbody>";
    foreach ($mesures as $m) {
        echo "<tr>
                <td>{$m['date_mesure']}</td>
                <td>{$m['masse']}</td>
                <td>
                    <a href='?supprimer={$m['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Supprimer cette mesure ?\")'>🗑</a>
                </td>
              </tr>";
    }
    echo "</tbody></table>";

    // --- Graphique par client ---
    $dates = json_encode(array_column($mesures, 'date_mesure'));
    $poids = json_encode(array_column($mesures, 'masse'));
    $color = $colors[$colorIndex % count($colors)];

    echo "<canvas id='chart{$client['id']}' height='100'></canvas>";
    echo "
    <script>
    const ctx{$client['id']} = document.getElementById('chart{$client['id']}');
    new Chart(ctx{$client['id']}, {
        type: 'line',
        data: {
            labels: $dates,
            datasets: [{
                label: 'Masse corporelle (kg)',
                data: $poids,
                borderColor: '$color',
                backgroundColor: '$color' + '33',
                tension: 0.3,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '$color'
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: false }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
    </script>";

    echo "</div></div>";
    $colorIndex++;
}
?>
</div>

<?php include 'footer.html'; ?>
