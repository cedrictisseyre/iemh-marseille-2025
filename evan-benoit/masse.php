<?php
require_once 'connexion.php';
include 'header.php';

// Gestion ajout/suppression/modification AVANT l'affichage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $id_client = $_POST['id_client'];
    $date_mesure = $_POST['date_mesure'];
    $masse = $_POST['masse'];

    $stmt = $conn->prepare("INSERT INTO suivi_masse (id_client, date_mesure, masse) VALUES (:id_client, :date_mesure, :masse)");
    $stmt->execute([':id_client' => $id_client, ':date_mesure' => $date_mesure, ':masse' => $masse]);

    header("Location: masse.php");
    exit;
}

if (isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $date_mesure = $_POST['date_mesure'];
    $masse = $_POST['masse'];
    $stmt = $conn->prepare("UPDATE suivi_masse SET date_mesure = :date_mesure, masse = :masse WHERE id = :id");
    $stmt->execute([':date_mesure' => $date_mesure, ':masse' => $masse, ':id' => $id]);
    header("Location: masse.php");
    exit;
}

if (isset($_GET['supprimer'])) {
    $id_mesure = intval($_GET['supprimer']);
    $stmt = $conn->prepare("DELETE FROM suivi_masse WHERE id = :id");
    $stmt->execute([':id' => $id_mesure]);
    header("Location: masse.php");
    exit;
}

// Récupérer la liste de tous les clients
$clients = $conn->query("SELECT id, prenom, nom FROM clients ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="text-center my-4">
    <h1>⚖️ Suivi de la masse corporelle</h1>
    <a href="index.php" class="btn btn-secondary mt-3">🏠 Retour à l'accueil</a>
</div>

<?php
// Couleurs variées pour les graphiques
$colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6610f2', '#6f42c1', '#fd7e14'];
$colorIndex = 0;

foreach ($clients as $client):
    echo "<div class='card shadow-sm mb-5'>";
    echo "<div class='card-header bg-dark text-white'>
            <strong>{$client['prenom']} {$client['nom']}</strong>
          </div>";
    echo "<div class='card-body'>";

    // Récupération des mesures
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

    // Tableau des mesures
    if (count($mesures) > 0) {
        echo "<table class='table table-striped text-center align-middle'>
                <thead class='table-light'>
                    <tr><th>Date</th><th>Masse (kg)</th><th>Actions</th></tr>
                </thead>
                <tbody>";
        foreach ($mesures as $m) {
            echo "<tr>
                    <form method='POST'>
                        <td>
                            <input type='hidden' name='id' value='{$m['id']}'>
                            <input type='date' name='date_mesure' class='form-control text-center' value='{$m['date_mesure']}' required>
                        </td>
                        <td>
                            <input type='number' step='0.1' name='masse' class='form-control text-center' value='{$m['masse']}' required>
                        </td>
                        <td>
                            <button type='submit' name='modifier' class='btn btn-warning btn-sm'>✏️</button>
                            <a href='?supprimer={$m['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Supprimer cette mesure ?\")'>🗑</a>
                        </td>
                    </form>
                  </tr>";
        }
        echo "</tbody></table>";

        // Données pour le graphique
        $dates = json_encode(array_column($mesures, 'date_mesure'));
        $poids = json_encode(array_column($mesures, 'masse'));
        $color = $colors[$colorIndex % count($colors)];
        echo "<canvas id='chart{$client['id']}' height='100'></canvas>";
        echo "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chart{$client['id']}').getContext('2d');
            new Chart(ctx, {
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
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Date' } },
                        y: { title: { display: true, text: 'Masse (kg)' }, beginAtZero: false }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
        </script>";
    } else {
        echo "<p class='text-muted text-center'>Aucune mesure enregistrée pour ce client.</p>";
    }

    echo "</div></div>";
    $colorIndex++;
endforeach;
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include 'footer.php'; ?>
