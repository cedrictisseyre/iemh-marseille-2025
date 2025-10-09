<?php
require_once 'connexion.php';
include 'header.html';

// ⚙️ AJOUT / MODIF / SUPPR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ➕ AJOUT
    if (isset($_POST['ajouter'])) {
        $id_client = $_POST['id_client'];
        $date_mesure = $_POST['date_mesure'];
        $masse = $_POST['masse'];

        $stmt = $conn->prepare("INSERT INTO suivi_masse (id_client, date_mesure, masse) VALUES (:id_client, :date_mesure, :masse)");
        $stmt->execute([':id_client' => $id_client, ':date_mesure' => $date_mesure, ':masse' => $masse]);
        header("Location: masse.php");
        exit;
    }

    // ✏️ MODIFIER
    if (isset($_POST['modifier'])) {
        $id_mesure = $_POST['id_mesure'];
        $date_mesure = $_POST['date_mesure'];
        $masse = $_POST['masse'];

        $stmt = $conn->prepare("UPDATE suivi_masse SET date_mesure = :date_mesure, masse = :masse WHERE id = :id");
        $stmt->execute([':date_mesure' => $date_mesure, ':masse' => $masse, ':id' => $id_mesure]);
        header("Location: masse.php");
        exit;
    }
}

// 🗑️ SUPPR
if (isset($_GET['supprimer'])) {
    $id_mesure = intval($_GET['supprimer']);
    $stmt = $conn->prepare("DELETE FROM suivi_masse WHERE id = :id");
    $stmt->execute([':id' => $id_mesure]);
    header("Location: masse.php");
    exit;
}

// 👥 Clients
$clients = $conn->query("SELECT id, prenom, nom FROM clients ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="text-center my-4">
    <h1>⚖️ Suivi de la masse corporelle</h1>
    <a href="index.php" class="btn btn-secondary mt-3">🏠 Retour à l'accueil</a>
</div>

<?php
$colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6610f2', '#6f42c1', '#fd7e14'];
$colorIndex = 0;
$chartsData = []; // Stockera les données pour Chart.js

foreach ($clients as $client) {
    echo "<div class='card shadow-sm mb-5'>";
    echo "<div class='card-header bg-dark text-white'><strong>{$client['prenom']} {$client['nom']}</strong></div>";
    echo "<div class='card-body'>";

    // Récupérer mesures
    $stmt = $conn->prepare("SELECT id, date_mesure, masse FROM suivi_masse WHERE id_client = :id_client ORDER BY date_mesure ASC");
    $stmt->execute([':id_client' => $client['id']]);
    $mesures = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formulaire d’ajout
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

    // Tableau
    if (count($mesures) > 0) {
        echo "<table class='table table-striped text-center align-middle'>
                <thead class='table-light'>
                    <tr><th>Date</th><th>Masse (kg)</th><th>Actions</th></tr>
                </thead><tbody>";

        foreach ($mesures as $m) {
            echo "<tr>
                <form method='POST' class='d-inline'>
                    <input type='hidden' name='id_mesure' value='{$m['id']}'>
                    <td><input type='date' name='date_mesure' value='{$m['date_mesure']}' class='form-control form-control-sm'></td>
                    <td><input type='number' step='0.1' name='masse' value='{$m['masse']}' class='form-control form-control-sm'></td>
                    <td>
                        <button type='submit' name='modifier' class='btn btn-warning btn-sm me-1'>✏️</button>
                        <a href='?supprimer={$m['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Supprimer cette mesure ?\")'>🗑</a>
                    </td>
                </form>
            </tr>";
        }

        echo "</tbody></table>";

        // Données pour graphique
        $dates = array_column($mesures, 'date_mesure');
        $masses = array_column($mesures, 'masse');
        $chartsData[] = [
            'id' => $client['id'],
            'dates' => $dates,
            'masses' => $masses,
            'color' => $colors[$colorIndex % count($colors)],
        ];

        // Canvas sous le tableau
        echo "<canvas id='chart{$client['id']}' height='100'></canvas>";
    } else {
        echo "<p class='text-muted text-center'>Aucune mesure enregistrée pour ce client.</p>";
    }

    echo "</div></div>";
    $colorIndex++;
}
?>

<!-- 📊 Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartsData = <?php echo json_encode($chartsData); ?>;

    chartsData.forEach(client => {
        const ctx = document.getElementById('chart' + client.id);
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: client.dates,
                datasets: [{
                    label: 'Masse corporelle (kg)',
                    data: client.masses,
                    borderColor: client.color,
                    backgroundColor: client.color + '33',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: client.color
                }]
            },
            options: {
                scales: {
                    x: {
                        title: { display: true, text: 'Date' }
                    },
                    y: {
                        title: { display: true, text: 'Masse (kg)' },
                        beginAtZero: false
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    });
});
</script>

<?php include 'footer.html'; ?>
